<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CampusApplication;
use App\Models\User;
use App\Support\CampusContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $throttleKey = $this->throttleKey($request, $credentials['email']);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan masuk. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $credentials['campus_id'] = app(CampusContext::class)->id();

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(route('catalog.index'))
                ->with('success', 'Selamat datang kembali, '.Auth::user()->name.'!');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    private function throttleKey(Request $request, string $email): string
    {
        return Str::transliterate(Str::lower($email).'|'.$request->ip());
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $email = Str::lower(trim((string) $value));
                    $reserved = CampusApplication::query()
                        ->whereRaw('LOWER(email) = ?', [$email])
                        ->whereIn('status', ['pending', 'approved'])
                        ->whereNull('admin_created_at')
                        ->exists();

                    if ($reserved) {
                        $fail('Email ini sedang digunakan untuk pengajuan portal. Gunakan tautan aktivasi yang dikirim setelah disetujui.');
                    }
                },
            ],
            'nim_nip' => ['nullable', 'string', 'max:50'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim_nip' => $validated['nim_nip'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'mahasiswa',
        ]);

        Auth::login($user);

        return redirect()->route('catalog.index')
            ->with('success', 'Akun berhasil dibuat! Selamat datang di Campus Lost & Found.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('catalog.index')
            ->with('success', 'Kamu telah keluar dari akun.');
    }
}
