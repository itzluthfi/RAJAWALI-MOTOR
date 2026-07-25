<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class LoginController extends Controller
{
    public function create(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $data = $request->validate([
                'username' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);

            $kunci = Str::lower($data['username']).'|'.$request->ip();

            if (RateLimiter::tooManyAttempts($kunci, 5)) {
                $detik = RateLimiter::availableIn($kunci);
                $menit = (int) ceil($detik / 60);

                throw ValidationException::withMessages([
                    'username' => "Terlalu banyak percobaan gagal. Coba lagi dalam {$menit} menit.",
                ]);
            }

            if (! Auth::attempt($data, $request->boolean('ingat'))) {
                RateLimiter::hit($kunci, 900);

                throw ValidationException::withMessages([
                    'username' => 'Username atau kata sandi salah. Silakan coba lagi.',
                ]);
            }

            if (! $request->user()->aktif) {
                Auth::logout();

                throw ValidationException::withMessages([
                    'username' => 'Akun Anda sedang tidak aktif. Hubungi pemilik toko.',
                ]);
            }

            RateLimiter::clear($kunci);
            $request->session()->regenerate();

            return redirect()->intended(
                $request->user()->peran === 'kasir' ? route('kasir') : route('dashboard')
            );
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal proses login', ['pesan' => $e->getMessage()]);

            throw ValidationException::withMessages([
                'username' => 'Sistem mengalami gangguan saat memproses login. Silakan coba lagi.',
            ]);
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
