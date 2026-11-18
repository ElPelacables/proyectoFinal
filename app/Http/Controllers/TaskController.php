<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $query = Task::where('user_id', Auth::id());

        if ($request->filled('search')) {$search = $request->input('search');$query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "\%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->input('filter') === 'pendientes') {$query->where('estado', '!=', 'Completada');
        }

        $tasks =$query->orderBy('fecha_inicio')->get();

        $month = (int)$request->query('month', now()->month);
        $year = (int)$request->query('year', now()->year);

        return view('tasks.index', compact('tasks', 'month', 'year'));
    }

    public function create(): View
    {
        return view('tasks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated =$this->validateTask($request);$validated['user_id'] = Auth::id();

        Task::create($validated);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Tarea creada correctamente.');
    }

    public function show(Task $task): View
    {
        if ($task->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver esta tarea.');
        }

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        if ($task->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar esta tarea.');
        }

        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task$task): RedirectResponse
    {
        if ($task->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para modificar esta tarea.');
        }

        $validated = $this->validateTask($request);
        $task->update($validated);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Tarea actualizada correctamente.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        if ($task->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para eliminar esta tarea.');
        }

        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Tarea eliminada correctamente.');
    }

    private function validateTask(Request $request): array
    {
        return $request->validate(
            [
                'nombre' => ['required', 'string', 'max:255'],
                'descripcion' => ['nullable', 'string'],
                'fecha_inicio' => ['required', 'date'],
                'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
                'estado' => ['required', 'in:Pendiente,En progreso,Completada'],
            ],
            [
                'nombre.required' => 'El nombre de la tarea es obligatorio.',
                'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
                'fecha_fin.required' => 'La fecha de fin es obligatoria.',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
                'estado.required' => 'El estado de la tarea es obligatorio.',
                'estado.in' => 'El estado solo puede ser: Pendiente, En progreso o Completada.',
            ]
        );
    }
}