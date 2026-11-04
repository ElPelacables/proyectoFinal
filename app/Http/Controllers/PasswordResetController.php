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
    if ($user) {
    $token = Str::random(60); 
    PasswordResetToken::create([
        'user_id' => $user->id,
        'token' => $token,
        'expiry_date' => Carbon::now()->addMinutes(30)
    ]);

    // NUEVO: CA-03.3 - Enviar al correo registrado
    Mail::to($user->email)->send(new ResetPasswordMail($token));
}
        // ... (código de solicitarRecuperacion) ...
    }

    public function restablecerPassword(Request $request) {
        // ... (código de restablecerPassword) ...
    }
}
