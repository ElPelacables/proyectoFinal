<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
</head>
<body>
    <h2>Recuperar Contraseña</h2>
    
    <!-- Aquí mostramos el mensaje genérico si existe (CA-03.2) -->
    @if(session('message'))
        <p style="color: green;">{{ session('message') }}</p>
    @endif

    <!-- Formulario que envía los datos al controlador que ya hicimos -->
    <form action="/recuperar-password" method="POST">
        @csrf <!-- Muy importante en Laravel para evitar el error 419 -->
        <label for="email">Ingresa tu correo registrado:</label><br>
        <input type="email" name="email" id="email" required><br><br>
        
        <button type="submit">Enviar enlace de recuperación</button>
    </form>
    <br>
    <a href="/login">Volver al inicio de sesión</a>
</body>
</html>
