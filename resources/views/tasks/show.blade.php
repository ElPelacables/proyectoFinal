<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Tarea</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 600px; margin: 2rem auto; padding: 0 1rem; }
        .card { border: 1px solid #d1d5db; border-radius: 8px; padding: 1.5rem; }
        .btn { display: inline-block; padding: .5rem 1rem; border-radius: 5px; text-decoration: none; margin-top: 1rem; background: #e5e7eb; color: #111827; }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ $task->nombre }}</h1>
        <p><strong>Estado:</strong> {{ $task->estado }}</p>
        <p><strong>Fecha de inicio:</strong> {{ $task->fecha_inicio->format('Y-m-d') }}</p>
        <p><strong>Fecha de fin:</strong> {{ $task->fecha_fin->format('Y-m-d') }}</p>
        <p><strong>Descripción:</strong><br>{{ $task->descripcion ?: 'Sin descripción registrada.' }}</p>

        <a href="{{ route('tasks.edit', $task) }}" class="btn">Editar</a>
        <a href="{{ route('tasks.index') }}" class="btn">Volver al listado</a>
    </div>
</body>
</html>