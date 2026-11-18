<body>
    <h1>Bienvenido a tu panel</h1>

    <p><a href="{{ route('tasks.index') }}">Ir a Mis Tareas y Calendario</a></p>

    <!-- Formulario para cumplir CA-04.1 -->
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Cerrar Sesión</button>
    </form>
</body>
</html>