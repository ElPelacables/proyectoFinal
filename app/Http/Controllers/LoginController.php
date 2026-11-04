<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Llave única para limitar intentos basada en correo e IP (CA-05.1)
        $throttleKey = Str::lower(trim($data['email'])) . '|' . $request->ip();

        // Verificar si el usuario excede el límite de 5 intentos (CA-05.1 y CA-05.2)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);

            throw ValidationException::withMessages([
                'email' => "Demasiados intentos fallidos. Inténtalo de nuevo en {$minutes} minutos.", // Mensaje comprensible (CA-05.3)
            ]);
        }

        $credentials = [
            'email'    => Str::lower(trim($data['email'])),
            'password' => $data['password'],
        ];

        if (Auth::attempt($credentials)) {
            // Un inicio de sesión exitoso limpia y reinicia el contador (CA-05.5)
            RateLimiter::clear($throttleKey);

            $request->session()->regenerate();
            return redirect()->intended(route('inicio'));
        }

        // Si falla, aumentar el contador de intentos fallidos con bloqueo de 15 minutos (CA-05.1 y CA-05.2)
        RateLimiter::hit($throttleKey, 900);

        return back()
            ->withErrors(['email' => 'Correo o contraseña incorrectos.']) // Mensaje seguro sin revelar información (CA-05.3)
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate(); // Invalida la sesión en el servidor (CA-04.2)
        $request->session()->regenerateToken();

        return redirect()->route('login'); // Redirige al login (CA-04.3)
    }
}