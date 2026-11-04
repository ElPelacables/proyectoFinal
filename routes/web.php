<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.store');

Route::get('/registro', [RegisterController::class, 'create'])->name('register');
Route::post('/registro', [RegisterController::class, 'store'])->name('register.store');

Route::middleware('auth')->group(function () {
    Route::get('/inicio', function () {
        return view('inicio');
    })->name('inicio');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

use App\Http\Controllers\PasswordResetController;

// Ruta para solicitar el correo (Cumple CA-03.1)
Route::post('/recuperar-password', [PasswordResetController::class, 'solicitarRecuperacion']);

// Ruta para enviar la nueva contraseña con el token
Route::post('/restablecer-password', [PasswordResetController::class, 'restablecerPassword']);
// Ruta para MOSTRAR el formulario de recuperación
Route::get('/recuperar', function () {
    return view('recuperar');
});

// (Asegúrate de que esta ruta esté en web.php y no en api.php para que funcione con el formulario Blade)
Route::post('/recuperar-password', [\App\Http\Controllers\PasswordResetController::class, 'solicitarRecuperacion']);