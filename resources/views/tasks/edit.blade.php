<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tarea</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 600px; margin: 2rem auto; padding: 0 1rem; }
        .group { margin-bottom: 1rem; }
        label { display: block; font-weight: bold; margin-bottom: .3rem; }
        input, textarea, select { width: 100%; padding: .5rem; border: 1px solid #d1d5db; border-radius: 5px; }
        .error { color: #dc2626; font-size: .85rem; margin-top: .25rem; }
        .btn { padding: .5rem 1rem; border-radius: 5px; border: none; cursor: pointer; text-decoration: none; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-secondary { background: #e5e7eb; color: #111827; }
    </style>
</head>
<body>
    <h1>Editar Tarea</h1>

    <form action="{{ route('tasks.update', $task) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="group">
            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $task->nombre) }}">
            @error('nombre') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="group">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $task->descripcion) }}</textarea>
            @error('descripcion') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="group">
            <label for="fecha_inicio">Fecha de inicio *</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', $task->fecha_inicio->format('Y-m-d')) }}">
            @error('fecha_inicio') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="group">
            <label for="fecha_fin">Fecha de fin *</label>
            <input type="date" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin', $task->fecha_fin->format('Y-m-d')) }}">
            @error('fecha_fin') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="group">
            <label for="estado">Estado *</label>
            <select id="estado" name="estado">
                @foreach(['Pendiente', 'En progreso', 'Completada'] as $estado)
                    <option value="{{ $estado }}" {{ old('estado', $task->estado) === $estado ? 'selected' : '' }}>
                        {{ $estado }}
                    </option>
                @endforeach
            </select>
            @error('estado') <div class="error">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Tarea</button>
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>