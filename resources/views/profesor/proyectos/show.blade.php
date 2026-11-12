@extends('layouts.app')

@section('content')

<a href="{{ route('profesor.proyectos.index') }}"
   class="btn btn-outline-secondary mb-4">
    Volver a mis proyectos
</a>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">

        <h2 class="fw-bold">
            {{ $proyecto->propuesta->titulo }}
        </h2>

        <span class="badge bg-success mb-3">
            {{ ucfirst($proyecto->estado) }}
        </span>

        <hr>

        <p>
            <strong>Estudiante:</strong>
            {{ $proyecto->estudiante->name }}
        </p>

        <p>
            <strong>Correo:</strong>
            {{ $proyecto->estudiante->email }}
        </p>

        <p>
            <strong>Fecha de inicio:</strong>
            {{ $proyecto->fecha_inicio?->format('d/m/Y') ?? 'Sin definir' }}
        </p>

        <h5 class="mt-4">Descripción del proyecto</h5>
        <p style="white-space: pre-line;">{{ $proyecto->propuesta->descripcion }}</p>

        <h5>Objetivos</h5>
        <p style="white-space: pre-line;">{{ $proyecto->propuesta->objetivos ?: 'No registrados.' }}</p>

    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <h4 class="fw-bold">Entregas programadas</h4>
        @if($proyecto->estado === 'activo')
        <a href="{{ route('profesor.entregas.create', $proyecto) }}"
            class="btn btn-primary my-3">
            Nueva entrega
        </a>
        @endif

        @forelse($proyecto->entregas->sortBy('numero_orden') as $entrega)

            <div class="border rounded p-3 my-3">

                <h5>{{ $entrega->titulo }}</h5>

                <p>{{ $entrega->descripcion }}</p>

                <p>
                    <strong>Fecha límite:</strong>
                    {{ $entrega->fecha_limite?->format('d/m/Y') ?? 'Sin definir' }}
                </p>

                <span class="badge bg-secondary">
                    {{ ucfirst($entrega->estado) }}
                </span>
                <div class="mt-3">
                    <a href="{{ route('profesor.entregas.show', [$proyecto, $entrega]) }}"
                    class="btn btn-outline-primary btn-sm">
                    Ver documentos recibidos
                    </a>
                </div>
            </div>
        

        @empty

            <div class="alert alert-info mt-3">
                Todavía no has creado entregas para este proyecto.
            </div>

        @endforelse

    </div>
</div>

@if($proyecto->fase_evaluacion === 'pendiente_evaluacion_1')

    <div class="card shadow-sm mt-4">

        <div class="card-header text-white"
             style="background-color: #54266F;">
            <h5 class="mb-0">
                Evaluación 1 - Profesor director
            </h5>
        </div>

        <div class="card-body">

            <form method="POST"
                  action="{{ route('profesor.evaluacion-director.store', $proyecto) }}">

                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Calificación (0.0 a 5.0)
                    </label>

                    <input type="number"
                           name="nota"
                           class="form-control"
                           min="0"
                           max="5"
                           step="0.01"
                           value="{{ old('nota') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Observaciones
                    </label>

                    <textarea name="observaciones"
                              class="form-control"
                              rows="4">{{ old('observaciones') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Nueva fecha límite si la nota es menor de 3.0
                    </label>

                    <input type="date"
                           name="nueva_fecha_limite"
                           class="form-control"
                           min="{{ now()->addDay()->format('Y-m-d') }}"
                           value="{{ old('nueva_fecha_limite') }}">

                    <small class="text-muted">
                        Solo es obligatoria cuando la evaluación
                        no es aprobada.
                    </small>
                </div>

                <button type="submit"
                        class="btn text-white"
                        style="background-color: #F47721;">
                    Guardar evaluación
                </button>

            </form>

        </div>
    </div>

@endif

@php
    $evaluaciones = $proyecto->evaluacionesDirector()
        ->orderBy('numero_intento')
        ->get();
@endphp

@if($evaluaciones->isNotEmpty())

    <div class="card shadow-sm mt-4">

        <div class="card-header">
            <h5 class="mb-0">
                Historial de evaluación 1
            </h5>
        </div>

        <div class="card-body">

            @foreach($evaluaciones as $evaluacion)

                <div class="border rounded p-3 mb-3">

                    <h6>
                        Intento {{ $evaluacion->numero_intento }}
                    </h6>

                    <p>
                        <strong>Nota:</strong>
                        {{ number_format((float) $evaluacion->nota, 2) }}
                    </p>

                    <p>
                        <strong>Resultado:</strong>

                        @if($evaluacion->nota >= 3)
                            <span class="badge bg-success">
                                Aprobado
                            </span>
                        @else
                            <span class="badge bg-warning text-dark">
                                Requiere correcciones
                            </span>
                        @endif
                    </p>

                    @if($evaluacion->observaciones)
                        <p>
                            <strong>Observaciones:</strong>
                            {{ $evaluacion->observaciones }}
                        </p>
                    @endif

                    @if($evaluacion->nueva_fecha_limite)
                        <p>
                            <strong>Nuevo plazo:</strong>
                            {{ $evaluacion->nueva_fecha_limite->format('d/m/Y') }}
                        </p>
                    @endif

                </div>

            @endforeach

        </div>
    </div>

@endif 
{{--aca acaba la evaluacion 1--}} 



@php
    $resultadoFinal = app(
        \App\Services\CalculoNotaFinalService::class
    )->calcular($proyecto);
@endphp

@if($resultadoFinal)

    <div class="card shadow-sm mt-4 mb-4">

        <div class="card-header text-white"
             style="background-color: #54266F;">
            <h5 class="mb-0">
                Resultado final del proyecto
            </h5>
        </div>

        <div class="card-body p-4">

            <p class="text-muted">
                Las dos evaluaciones han sido completadas.
            </p>

            <div class="row g-3 mb-4">

                <div class="col-md-4">
                    <div class="border rounded p-3 text-center">
                        <h6>Evaluación 1</h6>
                        <h3 class="fw-bold">
                            {{ number_format($resultadoFinal['evaluacion_1'], 2) }}
                        </h3>
                        <small class="text-muted">
                            Profesor director
                        </small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded p-3 text-center">
                        <h6>Evaluación 2</h6>
                        <h3 class="fw-bold">
                            {{ number_format($resultadoFinal['evaluacion_2'], 2) }}
                        </h3>
                        <small class="text-muted">
                            Promedio de los dos jurados
                        </small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded p-3 text-center">
                        <h6>Nota final</h6>
                        <h3 class="fw-bold">
                            {{ number_format($resultadoFinal['nota_final'], 2) }}
                        </h3>
                        <small class="text-muted">
                            Sobre 5.00
                        </small>
                    </div>
                </div>

            </div>

            <div class="alert {{ $resultadoFinal['aprobado'] ? 'alert-success' : 'alert-danger' }}">

                @if($resultadoFinal['aprobado'])
                    <strong>Proyecto aprobado.</strong>
                @else
                    <strong>Proyecto no aprobado.</strong>
                @endif

            </div>

            <p class="text-muted mb-0">
                La nota final corresponde al promedio entre
                la evaluación del director y el promedio
                de las calificaciones de los dos jurados.
            </p>

        </div>
    </div>

@endif


@endsection