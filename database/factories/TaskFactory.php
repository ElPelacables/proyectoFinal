<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nombre' => fake()->sentence(3),
            'descripcion' => fake()->paragraph(),
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addDays(2)->format('Y-m-d'),
            'estado' => 'Pendiente',
        ];
    }
}