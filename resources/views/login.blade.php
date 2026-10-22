<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>login</title>
</head>
<body>
    <h1>Inicio de sesión</h1>

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form action="{{ route('login.store') }}" method="POST">
        @csrf
        <div>
            <label for="email">Correo:</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>
        <br>
        <div>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <br>
        <button type="submit">Ingresar</button>
        <p>¿No tienes una cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></p>
    </form>
</body>
</html>