<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña</title>
</head>
<body>
    <h2>Crea tu nueva contraseña</h2>

    <!-- Mostrar errores (ej. token inválido o contraseña débil CA-03.5) -->
    @if($errors->any())
        <div style="color: red;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/restablecer-password" method="POST">
        @csrf
        <!-- Campo oculto para enviar el token -->
        <input type="hidden" name="token" value="{{ $token }}">

        <label for="password">Nueva contraseña:</label><br>
        <input type="password" name="password" id="password" required>
        <small>Debe tener mínimo 8 caracteres, una mayúscula, una minúscula y un número.</small><br><br>

        <button type="submit">Guardar nueva contraseña</button>
    </form>
</body>
</html>