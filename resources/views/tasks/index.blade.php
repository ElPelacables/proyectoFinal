<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memento — Organizador de Tareas y Calendario</title>
    <link rel="stylesheet" href="{{ asset('memento/styles.css') }}">
</head>
<body style="margin:0; font-family:system-ui,-apple-system,sans-serif;">
    @php
        $user = Auth::user();
        $initials = collect(explode(' ', $user->name ?? 'U'))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
        $currentDate = \Carbon\Carbon::create($year, $month, 1);
        $prevDate = $currentDate->copy()->subMonth();
        $nextDate = $currentDate->copy()->addMonth();
        $daysInMonth = $currentDate->daysInMonth;
        $firstDayOfWeek = $currentDate->dayOfWeekIso;
        $pendingCount = $tasks->where('estado', '!=', 'Completada')->count();
    @endphp

    <div class="app-shell" style="display:flex; min-height:100vh; background:#bae0ff;">
        {{-- BARRA LATERAL IZQUIERDA (MEMENTO) --}}
        <aside class="sidebar" style="width:260px; background:#4596f3; color:#fff; padding:1.25rem; display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <div style="display:flex; align-items:center; gap:10px; font-weight:700; font-size:1.2rem; margin-bottom:1.5rem;">
                    <span style="width:28px; height:28px; background:#0f172a; border-radius:8px; display:inline-block;"></span>
                    <span>Recordery</span>
                </div>

                <a href="{{ route('tasks.create') }}" style="display:block; text-align:center; background:#fff; color:#111827; font-weight:600; padding:.75rem; border-radius:12px; text-decoration:none; margin-bottom:1.25rem;">
                    + Nuevo recordatorio
                </a>

                <nav style="display:flex; flex-direction:column; gap:8px;">
                    <a href="{{ route('tasks.index', ['filter' => 'pendientes']) }}" style="display:flex; justify-content:space-between; align-items:center; padding:.65rem .9rem; border-radius:10px; background:{{ request('filter') === 'pendientes' ? '#fff' : 'rgba(255,255,255,.2)' }}; color:{{ request('filter') === 'pendientes' ? '#111827' : '#fff' }}; font-weight:600; text-decoration:none;">
                        <span>☆ Pendientes</span>
                        <span style="background:#fff; color:#111827; border-radius:999px; padding:1px 8px; font-size:.8rem; font-weight:700;">{{ $pendingCount }}</span>
                    </a>
                    <a href="{{ route('tasks.index') }}" style="display:block; padding:.65rem .9rem; border-radius:10px; background:{{ request('filter') !== 'pendientes' ? '#fff' : 'rgba(255,255,255,.2)' }}; color:{{ request('filter') !== 'pendientes' ? '#111827' : '#fff' }}; font-weight:600; text-decoration:none;">
                        📅 Mes
                    </a>
                </nav>
            </div>

            {{-- PERFIL DEL USUARIO AUTENTICADO --}}
            <div style="border-top:1px solid rgba(255,255,255,.25); padding-top:1rem;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:.75rem;">
                    <div style="width:36px; height:36px; border-radius:50%; background:#f97316; display:flex; align-items:center; justify-content:center; font-weight:700;">
                        {{ strtoupper($initials) }}
                    </div>
                    <div>
                        <div style="font-weight:600; font-size:.95rem;">{{ $user->name }}</div>
                        <div style="font-size:.75rem; opacity:.85;">{{ $user->email }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="width:100%; padding:.5rem; border:none; border-radius:8px; background:#0f172a; color:#fff; cursor:pointer; font-weight:600;">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </aside>

        {{-- CONTENIDO PRINCIPAL --}}
        <main style="flex:1; display:flex; flex-direction:column;">
            {{-- BARRA SUPERIOR --}}
            <header style="background:#56a3f5; padding:.85rem 2rem; display:flex; justify-content:space-between; align-items:center;">
                <form action="{{ route('tasks.index') }}" method="GET" style="width:360px;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Buscar recordatorios..."
                           style="width:100%; padding:.55rem 1rem; border-radius:10px; border:none; outline:none;">
                </form>

                <div style="display:flex; align-items:center; gap:12px;">
                    <span style="color:#fff; font-weight:600;">{{ $user->name }}</span>
                </div>
            </header>

            <div style="padding:1.75rem 2rem; max-width:1150px; width:100%; margin:0 auto;">
                @if(session('success'))
                    <div style="background:#dcfce7; color:#166534; padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem; font-weight:500;">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- SALUDO DINÁMICO --}}
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <div>
                        <div style="font-size:.75rem; font-weight:700; letter-spacing:.05em; color:#334155; text-transform:uppercase;">
                            {{ now()->translatedFormat('l, d \d\e F') }}
                        </div>
                        <h1 style="margin:.2rem 0; font-size:2rem; color:#0f172a;">Buenos días, {{ $user->name }}</h1>
                        <p style="margin:0; color:#334155;">Tienes {{ $pendingCount }} recordatorios activos en tu agenda.</p>
                    </div>
                    <a href="{{ route('tasks.create') }}" style="background:#fff; color:#0f172a; font-weight:600; padding:.7rem 1.2rem; border-radius:12px; text-decoration:none; box-shadow:0 2px 6px rgba(0,0,0,.06);">
                        + Crear recordatorio
                    </a>
                </div>

                {{-- TARJETA DEL CALENDARIO MENSUAL --}}
                <section style="background:#fff; border-radius:18px; overflow:hidden; box-shadow:0 8px 24px rgba(15,23,42,.08); margin-bottom:2rem;">
                    <div style="background:#56a3f5; color:#fff; padding:1rem 1.5rem; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <span style="font-size:.7rem; background:rgba(15,23,42,.25); padding:2px 8px; border-radius:4px; font-weight:700;">CALENDARIO</span>
                            <h2 style="margin:.25rem 0 0; font-size:1.35rem;">{{ ucfirst($currentDate->translatedFormat('F Y')) }}</h2>
                        </div>
                        <div style="display:flex; gap:8px;">
                            <a href="{{ route('tasks.index') }}" style="background:#fff; color:#0f172a; padding:.4rem .85rem; border-radius:8px; text-decoration:none; font-weight:600; font-size:.85rem;">Hoy</a>
                            <a href="{{ route('tasks.index', ['month' => $prevDate->month, 'year' => $prevDate->year]) }}" style="background:#fff; color:#0f172a; padding:.4rem .75rem; border-radius:8px; text-decoration:none; font-weight:700;">&lt;</a>
                            <a href="{{ route('tasks.index', ['month' => $nextDate->month, 'year' => $nextDate->year]) }}" style="background:#fff; color:#0f172a; padding:.4rem .75rem; border-radius:8px; text-decoration:none; font-weight:700;">&gt;</a>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(7, 1fr); border-bottom:1px solid #e2e8f0; background:#f8fafc;">
                        @foreach(['LUN', 'MAR', 'MIÉ', 'JUE', 'VIE', 'SÁB', 'DOM'] as $dayName)
                            <div style="padding:.65rem; text-align:center; font-size:.75rem; font-weight:700; color:#64748b;">{{ $dayName }}</div>
                        @endforeach
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(7, 1fr);">
                        @for($empty = 1; $empty < $firstDayOfWeek; $empty++)
                            <div style="min-height:92px; border-right:1px solid #f1f5f9; border-bottom:1px solid #f1f5f9; background:#f8fafc;"></div>
                        @endfor

                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $cellDate = \Carbon\Carbon::create($year, $month, $day)->startOfDay();
                                $isToday = $cellDate->isToday();
                                $dayTasks = $tasks->filter(fn($t) => $cellDate->between($t->fecha_inicio->startOfDay(), $t->fecha_fin->startOfDay()));
                            @endphp
                            <div style="min-height:92px; padding:.5rem; border-right:1px solid #f1f5f9; border-bottom:1px solid #f1f5f9;">
                                <div style="margin-bottom:.35rem;">
                                    <span style="display:inline-flex; width:24px; height:24px; align-items:center; justify-content:center; border-radius:50%; font-size:.8rem; font-weight:600; {{ $isToday ? 'background:#4f46e5; color:#fff;' : 'color:#475569;' }}">
                                        {{ $day }}
                                    </span>
                                </div>
                                @foreach($dayTasks as $t)
                                    <a href="{{ route('tasks.show', $t) }}" style="display:block; font-size:.75rem; color:#1e40af; background:#eff6ff; padding:2px 6px; border-radius:5px; margin-bottom:3px; text-decoration:none; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        {{ $t->nombre }}
                                    </a>
                                @endforeach
                            </div>
                        @endfor
                    </div>
                </section>

                {{-- TU AGENDA: TARJETAS DE RECORDATORIOS / TAREAS --}}
                <section>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                        <div>
                            <div style="font-size:.7rem; font-weight:700; color:#475569; letter-spacing:.05em;">TU AGENDA</div>
                            <h3 style="margin:.15rem 0 0; font-size:1.25rem; color:#0f172a;">Próximos recordatorios</h3>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(250px, 1fr)); gap:1rem;">
                        @forelse($tasks as $task)
                            <article style="background:#fff; border-radius:16px; padding:1.15rem; box-shadow:0 4px 12px rgba(15,23,42,.05); display:flex; flex-direction:column; justify-content:space-between;">
                                <div>
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.5rem;">
                                        <span style="font-size:.7rem; font-weight:700; text-transform:uppercase; color:#2563eb;">
                                            ● {{ $task->estado }}
                                        </span>
                                    </div>
                                    <h4 style="margin:0 0 .35rem; font-size:1.05rem; color:#0f172a;">{{ $task->nombre }}</h4>
                                    <p style="margin:0 0 .75rem; font-size:.85rem; color:#64748b;">
                                        {{ $task->fecha_inicio->format('d/m/Y') }} - {{ $task->fecha_fin->format('d/m/Y') }}
                                    </p>
                                    @if($task->descripcion)
                                        <p style="margin:0 0 .85rem; font-size:.82rem; color:#475569;">{{ Str::limit($task->descripcion, 70) }}</p>
                                    @endif
                                </div>

                                <div style="display:flex; gap:6px; border-top:1px solid #f1f5f9; padding-top:.75rem;">
                                    <a href="{{ route('tasks.show', $task) }}" style="flex:1; text-align:center; padding:.35rem; border-radius:8px; background:#f1f5f9; color:#0f172a; text-decoration:none; font-size:.8rem; font-weight:600;">Ver</a>
                                    <a href="{{ route('tasks.edit', $task) }}" style="flex:1; text-align:center; padding:.35rem; border-radius:8px; background:#e0f2fe; color:#0369a1; text-decoration:none; font-size:.8rem; font-weight:600;">Editar</a>
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta tarea?');" style="flex:1;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="width:100%; padding:.35rem; border:none; border-radius:8px; background:#fee2e2; color:#b91c1c; cursor:pointer; font-size:.8rem; font-weight:600;">Eliminar</button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <div style="grid-column:1/-1; background:#fff; border-radius:16px; padding:2rem; text-align:center; color:#64748b;">
                                No tienes recordatorios registrados todavía. Haz clic en <strong>+ Nuevo recordatorio</strong> para crear el primero.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script src="{{ asset('memento/script.js') }}"></script>
</body>
</html>