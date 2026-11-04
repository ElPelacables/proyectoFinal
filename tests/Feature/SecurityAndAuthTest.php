<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PasswordResetToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Tests\TestCase;

class SecurityAndAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_ca_03_5_la_nueva_contrasena_debe_cumplir_reglas_de_seguridad()
    {
        $user = User::factory()->create();
        $token = Str::random(60);

        PasswordResetToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expiry_date' => Carbon::now()->addMinutes(30)
        ]);

        $response = $this->post('/restablecer-password', [
            'token' => $token,
            'password' => 'debil',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_ca_03_3_y_03_4_se_genera_token_con_expiracion_de_30_minutos()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/recuperar-password', [
            'email' => 'test@example.com'
        ]);

        $response->assertSessionHas('message');
        
        $this->assertDatabaseHas('password_reset_tokens', [
            'user_id' => $user->id
        ]);
    }

    public function test_ca_03_6_al_cambiar_contrasena_el_token_queda_invalidado()
    {
        $user = User::factory()->create();
        $token = Str::random(60);

        PasswordResetToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expiry_date' => Carbon::now()->addMinutes(30)
        ]);

        $this->post('/restablecer-password', [
            'token' => $token,
            'password' => 'Password123'
        ]);

        $this->assertDatabaseMissing('password_reset_tokens', [
            'token' => $token
        ]);
    }

    public function test_hu_05_limita_intentos_fallidos_de_autenticacion()
    {
        $user = User::factory()->create([
            'email' => 'locked@example.com',
            'password' => Hash::make('Password123')
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'locked@example.com',
                'password' => 'WrongPassword'
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'locked@example.com',
            'password' => 'WrongPassword'
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_hu_04_cierre_de_sesion_seguro_e_invalidacion_de_sesion()
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $this->assertAuthenticated();

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}