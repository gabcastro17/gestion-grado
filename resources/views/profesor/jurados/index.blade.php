
@extends('layouts.app')

@section('content')

<div class="container py-4">

    <h2 class="fw-bold mb-4">
        Mis invitaciones como jurado
    </h2>

    <a href="{{ route('panel') }}"
       class="btn btn-outline-secondary mb-4">
        Volver al panel
    </a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @forelse($invitaciones as $invitacion)

        @php
            $proyecto = $invitacion->proyecto;

            $juradosAceptados = $proyecto
                ? $proyecto->jurados()
                    ->where('estado', 'aceptado')
                    ->count()
                : 0;

            $calificacion = $invitacion->calificacion;

            $puedeCalificar =
                $invitacion->estado === 'aceptado'
                && $proyecto
                && $proyecto->estado === 'activo'
                && in_array(
                    $proyecto->fase_evaluacion,
                    ['pendiente_jurados', 'evaluacion_2'],
                    true
                )
                && $juradosAceptados === 2
                && !$calificacion;
        @endphp

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-4">

                <h5 class="fw-bold">
                    {{ $proyecto?->propuesta?->titulo ?? 'Proyecto de grado' }}
                </h5>

                <p class="mb-2">
                    <strong>Estado de la invitación:</strong>

                    @if($invitacion->estado === 'pendiente')
                        <span class="badge bg-warning text-dark">
                            Pendiente
                        </span>
                    @elseif($invitacion->estado === 'aceptado')
                        <span class="badge bg-success">
                            Aceptada
                        </span>
                    @else
                        <span class="badge bg-secondary">
                            Rechazada
                        </span>
                    @endif
                </p>

                @if($invitacion->estado === 'pendiente')

                    <div class="d-flex gap-2 mt-3">

                        <form method="POST"
                              action="{{ route('profesor.jurados.aceptar', $invitacion) }}">
                            @csrf

                            <button type="submit"
                                    class="btn btn-success">
                                Aceptar
                            </button>
                        </form>

                        <form method="POST"
                              action="{{ route('profesor.jurados.rechazar', $invitacion) }}">
                            @csrf

                            <button type="submit"
                                    class="btn btn-outline-danger"
                                    onclick="return confirm('¿Rechazar esta invitación?')">
                                Rechazar
                            </button>
                        </form>

                    </div>

                @elseif($invitacion->estado === 'aceptado')

                    <hr>

                    <h6 class="fw-bold">
                        Evaluación 2
                    </h6>

                    <p class="text-muted">
                        Jurados aceptados:
                        {{ $juradosAceptados }} de 2
                    </p>

                    @if($calificacion)

                        <div class="alert alert-success">
                            <strong>Ya registraste tu calificación.</strong>

                            <p class="mb-1 mt-2">
                                Nota:
                                <strong>
                                    {{ number_format((float) $calificacion->nota, 2) }}
                                </strong>
                                / 5.00
                            </p>

                            <p class="mb-0">
                                <strong>Observaciones:</strong>
                                {{ $calificacion->observaciones ?: 'Sin observaciones.' }}
                            </p>
                        </div>

                    @elseif($puedeCalificar)

                        <form method="POST"
                              action="{{ route('profesor.jurados.calificar', $invitacion) }}">
                            @csrf

                            <div class="mb-3">
                                <label for="nota-{{ $invitacion->id }}"
                                       class="form-label">
                                    Calificación (0.0 a 5.0)
                                </label>

                                <input
                                    type="number"
                                    id="nota-{{ $invitacion->id }}"
                                    name="nota"
                                    class="form-control"
                                    min="0"
                                    max="5"
                                    step="0.01"
                                    value="{{ old('nota') }}"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label for="observaciones-{{ $invitacion->id }}"
                                       class="form-label">
                                    Observaciones
                                </label>

                                <textarea
                                    id="observaciones-{{ $invitacion->id }}"
                                    name="observaciones"
                                    class="form-control"
                                    rows="4"
                                    maxlength="5000"
                                    placeholder="Escribe tus observaciones sobre el proyecto..."
                                >{{ old('observaciones') }}</textarea>
                            </div>

                            <button type="submit"
                                    class="btn btn-primary"
                                    onclick="return confirm('¿Confirmas esta calificación? No podrás registrarla nuevamente.')">
                                Guardar calificación
                            </button>
                        </form>

                    @elseif($juradosAceptados < 2)

                        <div class="alert alert-info">
                            Has aceptado la invitación.
                            Estamos esperando que el segundo jurado
                            acepte para habilitar la evaluación.
                        </div>

                    @else

                        <div class="alert alert-warning">
                            La evaluación de este proyecto
                            no está disponible.
                        </div>

                    @endif

                @endif

            </div>
        </div>

    @empty

        <div class="alert alert-info">
            Todavía no tienes invitaciones como jurado.
        </div>

    @endforelse

</div>

@endsection
