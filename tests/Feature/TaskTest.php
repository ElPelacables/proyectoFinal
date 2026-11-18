<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    // 1. CA-09: Visitante no autenticado no puede gestionar tareas
    public function test_guest_cannot_access_tasks(): void
    {
        $response = $this->get(route('tasks.index'));

        $response->assertRedirect('/login');
    }

    // 2. CA-01 y CA-03: Usuario autenticado crea tarea válida y se refleja en listado/calendario
    public function test_authenticated_user_can_create_task_and_see_it_in_calendar(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'nombre' => 'Entregar Sprint 3',
            'descripcion' => 'Subir evidencia de caja blanca',
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addDays(2)->format('Y-m-d'),
            'estado' => 'Pendiente',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'nombre' => 'Entregar Sprint 3',
            'estado' => 'Pendiente',
        ]);

        $this->actingAs($user)
            ->get(route('tasks.index'))
            ->assertSee('Entregar Sprint 3');
    }

    // 3. CA-02 y CA-11: Rama negativa -> rechaza fechas invertidas (fin anterior a inicio)
    public function test_cannot_create_task_with_end_date_before_start_date(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'nombre' => 'Tarea con fechas inválidas',
            'descripcion' => 'Prueba rama false de fecha_fin',
            'fecha_inicio' => '2026-11-20',
            'fecha_fin' => '2026-11-18',
            'estado' => 'Pendiente',
        ]);

        $response->assertSessionHasErrors(['fecha_fin']);
        $this->assertDatabaseCount('tasks', 0);
    }

    // 4. CA-02 y CA-10: Rama negativa -> rechaza nombre vacío y estado no permitido
    public function test_cannot_create_task_with_empty_name_or_invalid_status(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'nombre' => '',
            'fecha_inicio' => '2026-11-20',
            'fecha_fin' => '2026-11-21',
            'estado' => 'Cancelada',
        ]);

        $response->assertSessionHasErrors(['nombre', 'estado']);
    }

    // 5. CP-01 (Caja Blanca): Usuario sin privilegios sobre tarea ajena recibe HTTP 403
    public function test_user_cannot_view_edit_update_or_delete_another_users_task(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($intruder)->get(route('tasks.show', $task))->assertStatus(403);
        $this->actingAs($intruder)->get(route('tasks.edit', $task))->assertStatus(403);
        $this->actingAs($intruder)->put(route('tasks.update', $task), [
            'nombre' => 'Hack',
            'fecha_inicio' => '2026-11-20',
            'fecha_fin' => '2026-11-21',
            'estado' => 'Completada',
        ])->assertStatus(403);
        $this->actingAs($intruder)->delete(route('tasks.destroy', $task))->assertStatus(403);
    }

    // 6. CA-04, CA-05 y CA-06: Consultar y editar tarea propia
    public function test_user_can_view_and_update_own_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'nombre' => 'Tarea inicial',
            'estado' => 'Pendiente',
        ]);

        $this->actingAs($user)
            ->get(route('tasks.show', $task))
            ->assertStatus(200)
            ->assertSee('Tarea inicial');

        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'nombre' => 'Tarea actualizada',
            'descripcion' => 'Descripción editada',
            'fecha_inicio' => '2026-11-20',
            'fecha_fin' => '2026-11-22',
            'estado' => 'Completada',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'nombre' => 'Tarea actualizada',
            'estado' => 'Completada',
        ]);
    }

    // 7. CA-07 y CA-08: Eliminar tarea propia
    public function test_user_can_delete_own_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    // 8. CA-12: Operación sobre tarea inexistente devuelve HTTP 404
    public function test_non_existent_task_returns_404(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/tasks/99999')->assertStatus(404);
    }
}