<?php

namespace Tests\Unit;

use App\Http\Controllers\TaskController;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use ReflectionMethod;
use Tests\TestCase;

class TaskUnitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * PU-01 (CA-01): Verifica asignación masiva de los 5 campos, casts de fecha y relación user() en el modelo Task.
     */
    public function test_pu01_task_model_casts_dates_and_belongs_to_user(): void
    {
        $user = User::factory()->create();

        $task = Task::create([
            'user_id' => $user->id,
            'nombre' => 'Implementar pruebas unitarias',
            'descripcion' => 'Validar modelo Task y reglas de negocio',
            'fecha_inicio' => '2026-11-20',
            'fecha_fin' => '2026-11-25',
            'estado' => 'Pendiente',
        ]);

        $this->assertInstanceOf(Carbon::class, $task->fecha_inicio);
        $this->assertInstanceOf(Carbon::class, $task->fecha_fin);
        $this->assertEquals('Implementar pruebas unitarias', $task->nombre);
        $this->assertEquals('Pendiente', $task->estado);
        $this->assertTrue($task->user->is($user));
    }

    /**
     * PU-02 (CA-02 y CA-11): Verifica de forma aislada que validateTask() rechace fecha_fin anterior a fecha_inicio.
     */
    public function test_pu02_validation_rejects_end_date_before_start_date(): void
    {
        $controller = new TaskController();
        $method = new ReflectionMethod(TaskController::class, 'validateTask');
        $method->setAccessible(true);

        $request = Request::create('/tasks', 'POST', [
            'nombre' => 'Tarea con fechas cruzadas',
            'descripcion' => 'Prueba unitaria de validación',
            'fecha_inicio' => '2026-11-25',
            'fecha_fin' => '2026-11-20',
            'estado' => 'Pendiente',
        ]);

        try {
            $method->invoke($controller, $request);
            $this->fail('Se esperaba ValidationException por fecha_fin anterior a fecha_inicio.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('fecha_fin', $e->errors());
            $this->assertSame(
                'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
                $e->errors()['fecha_fin'][0]
            );
        }
    }

    /**
     * PU-03 (CA-10): Verifica que la validación solo acepte Pendiente, En progreso o Completada.
     */
    public function test_pu03_validation_enforces_allowed_statuses_only(): void
    {
        $controller = new TaskController();
        $method = new ReflectionMethod(TaskController::class, 'validateTask');
        $method->setAccessible(true);

        // Rama positiva: los 3 estados permitidos pasan la validación
        foreach (['Pendiente', 'En progreso', 'Completada'] as $validStatus) {
            $validRequest = Request::create('/tasks', 'POST', [
                'nombre' => 'Tarea válida',
                'fecha_inicio' => '2026-11-20',
                'fecha_fin' => '2026-11-21',
                'estado' => $validStatus,
            ]);

            $validated = $method->invoke($controller, $validRequest);
            $this->assertSame($validStatus, $validated['estado']);
        }

        // Rama negativa: un estado fuera de dominio lanza ValidationException
        $invalidRequest = Request::create('/tasks', 'POST', [
            'nombre' => 'Tarea con estado inválido',
            'fecha_inicio' => '2026-11-20',
            'fecha_fin' => '2026-11-21',
            'estado' => 'Cancelada',
        ]);

        $this->expectException(ValidationException::class);
        $method->invoke($controller, $invalidRequest);
    }

    /**
     * PU-04 (CA-03 y CA-05): Verifica actualización de atributos en el modelo y pertenencia al rango de fechas del calendario.
     */
    public function test_pu04_task_model_updates_attributes_and_evaluates_date_range(): void
    {
        $task = Task::factory()->create([
            'nombre' => 'Borrador inicial',
            'fecha_inicio' => '2026-11-10',
            'fecha_fin' => '2026-11-15',
            'estado' => 'Pendiente',
        ]);

        $task->update([
            'nombre' => 'Entrega final',
            'estado' => 'Completada',
        ]);

        $task->refresh();

        $this->assertSame('Entrega final', $task->nombre);
        $this->assertSame('Completada', $task->estado);

        // Verifica lógica de rango usada por el calendario
        $insideDate = Carbon::parse('2026-11-12');
        $outsideDate = Carbon::parse('2026-11-18');

        $this->assertTrue($insideDate->between($task->fecha_inicio, $task->fecha_fin));
        $this->assertFalse($outsideDate->between($task->fecha_inicio, $task->fecha_fin));
    }
}