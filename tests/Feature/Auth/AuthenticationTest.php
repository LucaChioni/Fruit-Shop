<?php

namespace Tests\Feature\Auth;

use App\Mail\EmailLoginCodeMail;
use App\Models\EmailLoginCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_login_sends_email_code(): void
    {
        Mail::fake();

        $response = $this
            ->withSession(['locale' => 'en'])
            ->post('/login', [
                'email' => 'test@example.com',
            ]);

        $response->assertSessionHas('email_login_email', 'test@example.com');
        $response->assertSessionHas('email_login_needs_name', true);
        $this->assertGuest();
        $this->assertSame(1, EmailLoginCode::where('email', 'test@example.com')->count());
        Mail::assertSent(EmailLoginCodeMail::class, fn (EmailLoginCodeMail $mail) => $mail->hasTo('test@example.com')
            && $mail->locale === 'en'
            && str_contains($mail->render(), 'Login code'));
    }

    public function test_login_code_requests_use_the_configured_email_daily_limit(): void
    {
        Mail::fake();
        $key = 'login-code:email:'.hash('sha256', 'test@example.com');

        foreach (range(1, config('auth.login_code.email_daily_limit')) as $attempt) {
            RateLimiter::hit($key, 86400);
        }

        $this->post('/login', ['email' => 'test@example.com'])
            ->assertSessionHasErrors([
                'email' => 'Hai richiesto troppi codici. Riprova domani.',
            ]);

        Mail::assertNothingSent();
    }

    public function test_login_code_requests_use_the_configured_ip_daily_limit(): void
    {
        Mail::fake();
        $ipAddress = '203.0.113.10';
        $key = 'login-code:ip:'.hash('sha256', $ipAddress);

        foreach (range(1, config('auth.login_code.ip_daily_limit')) as $attempt) {
            RateLimiter::hit($key, 86400);
        }

        $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
            ->post('/login', ['email' => 'test@example.com'])
            ->assertSessionHasErrors([
                'email' => 'Hai richiesto troppi codici. Riprova domani.',
            ]);

        Mail::assertNothingSent();
    }

    public function test_sessions_remain_active_for_ten_days_of_inactivity(): void
    {
        $this->assertSame(14400, config('session.lifetime'));
    }

    public function test_existing_users_can_verify_code_and_login(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        EmailLoginCode::create([
            'email' => 'test@example.com',
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->post(route('login.verify'), [
            'email' => 'test@example.com',
            'code' => '123456',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertSame(1, User::where('email', 'test@example.com')->count());
    }

    public function test_new_users_can_verify_code_create_account_and_login(): void
    {
        EmailLoginCode::create([
            'email' => 'new@example.com',
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->post(route('login.verify'), [
            'email' => 'new@example.com',
            'name' => 'New Customer',
            'code' => '123456',
        ]);

        $user = User::where('email', 'new@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertFalse($user->is_admin);
        $this->assertSame('New Customer', $user->name);
    }

    public function test_new_users_must_provide_name_to_verify_code(): void
    {
        EmailLoginCode::create([
            'email' => 'new@example.com',
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->post(route('login.verify'), [
            'email' => 'new@example.com',
            'code' => '123456',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertGuest();
        $this->assertNull(User::where('email', 'new@example.com')->first());
    }

    public function test_users_can_not_authenticate_with_invalid_code(): void
    {
        EmailLoginCode::create([
            'email' => 'test@example.com',
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->post(route('login.verify'), [
            'email' => 'test@example.com',
            'code' => '654321',
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_otp_verification_attempts_remain_limited_when_ip_changes(): void
    {
        $this->assertSame(5, config('auth.login_code.verification_attempt_limit'));
        $this->assertSame(25, config('auth.login_code.verification_ip_attempt_limit'));

        config()->set('auth.login_code.verification_attempt_limit', 2);

        User::factory()->create(['email' => 'test@example.com']);

        EmailLoginCode::create([
            'email' => 'test@example.com',
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $limitedIp = '203.0.113.20';
        $invalidMessage = __('ui.validation.code_invalid', [], 'it');

        foreach (range(1, 2) as $attempt) {
            $this->withServerVariables(['REMOTE_ADDR' => $limitedIp])
                ->post(route('login.verify'), [
                    'email' => 'test@example.com',
                    'code' => '654321',
                ])
                ->assertSessionHasErrors(['code' => $invalidMessage]);
        }

        $this->withServerVariables(['REMOTE_ADDR' => $limitedIp])
            ->post(route('login.verify'), [
                'email' => 'test@example.com',
                'code' => '123456',
            ])
            ->assertSessionHasErrors(['code' => $invalidMessage]);

        $this->assertGuest();

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.21'])
            ->post(route('login.verify'), [
                'email' => 'test@example.com',
                'code' => '123456',
            ])
            ->assertSessionHasErrors(['code' => $invalidMessage]);

        $this->assertGuest();
    }

    public function test_otp_verification_attempts_are_limited_per_ip_across_emails(): void
    {
        config()->set('auth.login_code.verification_ip_attempt_limit', 1);

        foreach (['first@example.com', 'second@example.com'] as $email) {
            EmailLoginCode::create([
                'email' => $email,
                'code_hash' => Hash::make('123456'),
                'expires_at' => now()->addMinutes(10),
            ]);
        }

        $ipAddress = '203.0.113.22';
        $invalidMessage = __('ui.validation.code_invalid', [], 'it');

        $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
            ->post(route('login.verify'), [
                'email' => 'first@example.com',
                'code' => '654321',
            ])
            ->assertSessionHasErrors(['code' => $invalidMessage]);

        $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
            ->post(route('login.verify'), [
                'email' => 'second@example.com',
                'code' => '123456',
            ])
            ->assertSessionHasErrors(['code' => $invalidMessage]);

        $this->assertGuest();
    }

    public function test_otp_verification_attempts_are_reset_after_success(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $ipAddress = '203.0.113.30';
        $emailRateLimitKey = 'login-code-verification:email:'.hash('sha256', 'test@example.com');
        $ipRateLimitKey = 'login-code-verification:ip:'.hash('sha256', $ipAddress);

        EmailLoginCode::create([
            'email' => 'test@example.com',
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        RateLimiter::hit($emailRateLimitKey, 600);
        RateLimiter::hit($ipRateLimitKey, 600);

        $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
            ->post(route('login.verify'), [
                'email' => 'test@example.com',
                'code' => '123456',
            ])
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
        $this->assertSame(0, RateLimiter::attempts($emailRateLimitKey));
        $this->assertSame(2, RateLimiter::attempts($ipRateLimitKey));
        $this->assertSame(0, RateLimiter::availableIn($emailRateLimitKey));
        $this->assertGreaterThan(0, RateLimiter::availableIn($ipRateLimitKey));
    }

    public function test_valid_otp_can_be_retried_when_name_is_missing(): void
    {
        config()->set('auth.login_code.verification_attempt_limit', 2);

        EmailLoginCode::create([
            'email' => 'new@example.com',
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $ipAddress = '203.0.113.31';
        $emailRateLimitKey = 'login-code-verification:email:'.hash('sha256', 'new@example.com');
        $ipRateLimitKey = 'login-code-verification:ip:'.hash('sha256', $ipAddress);

        $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
            ->post(route('login.verify'), [
                'email' => 'new@example.com',
                'code' => '123456',
            ])
            ->assertSessionHasErrors('name');

        $this->assertSame(1, RateLimiter::attempts($emailRateLimitKey));
        $this->assertSame(1, RateLimiter::attempts($ipRateLimitKey));

        $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
            ->post(route('login.verify'), [
                'email' => 'new@example.com',
                'name' => 'New Customer',
                'code' => '123456',
            ])
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->assertSame(0, RateLimiter::attempts($emailRateLimitKey));
        $this->assertSame(2, RateLimiter::attempts($ipRateLimitKey));
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
