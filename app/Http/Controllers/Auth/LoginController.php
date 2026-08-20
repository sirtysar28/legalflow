<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\CaptchaController;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
            'captcha'  => ['required', 'string'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'captcha.required'  => 'Kode keamanan wajib diisi.',
        ]);

        // Verifikasi captcha SEBELUM memeriksa kredensial (sekali pakai,
        // case-insensitive, kedaluwarsa 10 menit).
        if (! CaptchaController::verify($validated['captcha'] ?? null)) {
            return back()->withErrors(['captcha' => 'Kode keamanan salah atau sudah kedaluwarsa.'])
                ->withInput($request->only('email'));
        }

        $remember = $request->boolean('remember');

        $credentials = $request->only('email', 'password');

        if (! auth()->attempt($credentials, $remember)) {
            return back()->withErrors(['email' => 'Email atau password salah.'])
                ->withInput($request->only('email'));
        }

        if (! auth()->user()->isActive()) {
            auth()->logout();

            return back()->withErrors(['email' => 'Akun Anda tidak aktif. Hubungi Admin.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
