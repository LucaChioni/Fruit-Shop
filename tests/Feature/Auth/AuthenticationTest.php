<?php

namespace Tests\Feature\Auth;

use App\Mail\EmailLoginCodeMail;
use App\Models\EmailLoginCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

        $response = $this->post('/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHas('email_login_email', 'test@example.com');
        $response->assertSessionHas('email_login_needs_name', true);
        $this->assertGuest();
        $this->assertSame(1, EmailLoginCode::where('email', 'test@example.com')->count());
        Mail::assertSent(EmailLoginCodeMail::class, fn (EmailLoginCodeMail $mail) => $mail->hasTo('test@example.com'));
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

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
