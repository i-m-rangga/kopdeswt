<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('username', 'password'), $this->boolean('remember'))) {
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        if ($user->status !== 'aktif') {
            Auth::logout();
            throw ValidationException::withMessages([
                'username' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! $this->hasTooManyLoginAttempts()) {
            return;
        }

        throw ValidationException::withMessages([
            'username' => __('auth.throttle', [
                'seconds' => $this->limiter()->availableIn(),
            ]),
        ]);
    }

    /**
     * Determine if the request has too many login attempts.
     */
    public function hasTooManyLoginAttempts(): bool
    {
        return $this->limiter()->tooManyAttempts();
    }

    /**
     * Get the rate limiter instance.
     */
    public function limiter(): \Illuminate\Cache\RateLimiter
    {
        return app(\Illuminate\Cache\RateLimiter::class);
    }
}