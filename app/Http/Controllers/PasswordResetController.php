<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordResetToken;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    public function solicitarRecuperacion(Request $request) {
        // 1. Buscamos si el correo ingresado en el formulario existe en la base de datos
        $user = User::where('email', $request->email)->first();

        // 2. Si existe, generamos el token y enviamos el correo
        if ($user) {
            $token = Str::random(60); 
            
            PasswordResetToken::create([
                'user_id' => $user->id,
                'token' => $token,
                'expiry_date' => Carbon::now()->addMinutes(30)
            ]);

            Mail::to($user->email)->send(new ResetPasswordMail($token));
        }

        // 3. Devolvemos al usuario a la vista con el mensaje genérico (CA-03.2)
        return back()->with('message', 'Si el correo está registrado, recibirás un enlace de recuperación.');
    }

    public function restablecerPassword(Request $request) {
        // 1. Buscamos el token en la base de datos
        $resetToken = PasswordResetToken::where('token', $request->token)->first();

        // 2. Validamos si existe y si no ha expirado
        if (!$resetToken || $resetToken->expiry_date->isPast()) {
            if ($resetToken) $resetToken->delete();
            return back()->withErrors(['error' => 'El token es inválido o ha expirado.']);
        }

        // 3. Validamos las reglas de seguridad de la nueva contraseña (CA-03.5)
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/']
        ]);

        // 4. Cambiamos la contraseña y la encriptamos
        $user = $resetToken->user;
        $user->password = Hash::make($request->password);
        $user->save();

        // 5. Borramos el token usado (CA-03.6)
        $resetToken->delete();

        // 6. Redirigimos al inicio de sesión
        return redirect('/login')->with('message', 'Contraseña actualizada exitosamente. Puedes iniciar sesión.');
    }
}