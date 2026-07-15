<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailLoginCodeMail;
use App\Models\EmailLoginCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'status' => session('status'),
            'emailLoginEmail' => session('email_login_email'),
            'emailLoginNeedsName' => session('email_login_needs_name', false),
        ]);
    }

    /**
     * Send the one-time login code.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|lowercase|email|max:255',
        ]);

        $email = $validated['email'];
        $code = (string) random_int(100000, 999999);
        $decaySeconds = max(1, now()->diffInSeconds(now()->endOfDay()));
        $emailRateLimitKey = 'login-code:email:'.hash('sha256', $email);
        $ipRateLimitKey = 'login-code:ip:'.hash('sha256', (string) $request->ip());

        if (RateLimiter::tooManyAttempts($emailRateLimitKey, config('auth.login_code.email_daily_limit'))
            || RateLimiter::tooManyAttempts($ipRateLimitKey, config('auth.login_code.ip_daily_limit'))) {
            throw ValidationException::withMessages([
                'email' => __('ui.validation.login_code_throttled'),
            ]);
        }

        RateLimiter::hit($emailRateLimitKey, $decaySeconds);
        RateLimiter::hit($ipRateLimitKey, $decaySeconds);

        EmailLoginCode::query()
            ->where('email', $email)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        EmailLoginCode::create([
            'email' => $email,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($email)->locale(app()->getLocale())->send(new EmailLoginCodeMail($code));

        return back()
            ->with('email_login_email', $email)
            ->with('email_login_needs_name', User::where('email', $email)->doesntExist());
    }

    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|lowercase|email|max:255',
            'code' => 'required|string|size:6',
        ], [
            'code.required' => __('ui.validation.code_required'),
            'code.size' => __('ui.validation.code_size'),
        ]);

        $email = $validated['email'];

        $loginCode = EmailLoginCode::query()
            ->where('email', $email)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $loginCode || ! Hash::check($validated['code'], $loginCode->code_hash)) {
            $request->session()->flash('email_login_email', $email);
            $request->session()->flash('email_login_needs_name', User::where('email', $email)->doesntExist());

            throw ValidationException::withMessages([
                'code' => __('ui.validation.code_invalid'),
            ]);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $nameValidator = Validator::make($request->only('name'), [
                'name' => 'required|string|max:255',
            ], [
                'name.required' => __('ui.validation.name_required'),
                'name.max' => __('ui.validation.name_max'),
            ]);

            if ($nameValidator->fails()) {
                $request->session()->flash('email_login_email', $email);
                $request->session()->flash('email_login_needs_name', true);

                throw new ValidationException($nameValidator);
            }

            $validated = array_merge($validated, $nameValidator->validated());
        }

        $loginCode->update(['used_at' => now()]);

        $user ??= User::create([
            'email' => $email,
            'name' => $validated['name'],
            'locale' => app()->getLocale(),
            'password' => Hash::make(Str::random(64)),
            'email_verified_at' => now(),
        ]);

        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
