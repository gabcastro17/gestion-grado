@extends('layouts.app')

@section('content')

<a href="{{ route('estudiante.proyectos.index') }}"
   class="btn btn-outline-secondary mb-4">
    Volver a mis proyectos
</a>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-start">
            <div>
                <p class="text-muted mb-1">Proyecto de grado</p>

                <h2 class="fw-bold">
                    {{ $proyecto->propuesta->titulo }}
                </h2>
            </div>

            <span class="badge bg-success">
                {{ ucfirst($proyecto->estado) }}
            </span>
        </div>

        <hr>

        <p>
            <strong>Profesor director:</strong>
            {{ $proyecto->profesor->name }}
        </p>

        <p>
            <strong>Correo del director:</strong>
            {{ $proyecto->profesor->email }}
        </p>

        <p>
            <strong>Fecha de inicio:</strong>
            {{ $proyecto->fecha_inicio?->format('d/m/Y') ?? 'Sin definir' }}
        </p>

        <h5 class="mt-4">Descripción</h5>
        <p style="white-space: pre-line;">{{ $proyecto->propuesta->descripcion }}</p>

        <h5>Objetivos</h5>
        <p style="white-space: pre-line;">{{ $proyecto->propuesta->objetivos ?: 'No registrados.' }}</p>

    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <h4 class="fw-bold mb-3">Entregas del proyecto</h4>

        @forelse($proyecto->entregas->sortBy('numero_orden') as $entrega)

            <div class="border rounded p-3 mb-3">

                <h5>{{ $entrega->titulo }}</h5>

                <p class="text-muted mb-2">
                    {{ $entrega->descripcion }}
                </p>

                <p class="mb-1">
                    <strong>Fecha límite:</strong>
                    {{ $entrega->fecha_limite?->format('d/m/Y') ?? 'Sin definir' }}
                </p>

                <span class="badge bg-secondary">
                    {{ ucfirst($entrega->estado) }}
                </span>
               <div class="mt-3">
                   <a href="{{ route('estudiante.entregas.show', [$proyecto, $entrega]) }}"
                    class="btn btn-primary btn-sm">
                     Ver entrega
                   </a>
                </div> 

            </div>

        @empty

            <div class="alert alert-info">
                Tu profesor todavía no ha programado entregas.
            </div>

        @endforelse

    </div>
</div>



@php
    $resultadoFinal = app(
        \App\Services\CalculoNotaFinalService::class
    )->calcular($proyecto);
@endphp

@if($resultadoFinal)

    <div class="card shadow-sm border-0 mt-4 mb-4">

        <div class="card-header text-white"
             style="background-color: #54266F;">
            <h5 class="mb-0">
                Resultado final de tu proyecto
            </h5>
        </div>

        <div class="card-body p-4">

            <p class="text-muted">
                Tu proyecto ha completado las dos etapas
                de evaluación.
            </p>

            <div class="row g-3 mb-4">

                <div class="col-md-4">
                    <div class="border rounded p-3 text-center">
                        <h6>Evaluación 1</h6>

                        <h3 class="fw-bold">
                            {{ number_format($resultadoFinal['evaluacion_1'], 2) }}
                        </h3>

                        <small class="text-muted">
                            Nota del profesor director
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
                            Calificación definitiva
                        </small>
                    </div>
                </div>

            </div>

            @if($resultadoFinal['aprobado'])

                <div class="alert alert-success mb-0">
                    <h5 class="fw-bold">
                        ¡Proyecto aprobado!
                    </h5>

                    Tu nota final es igual o superior a 3.0.
                    Has aprobado la evaluación de tu proyecto de grado.
                </div>

            @else

                <div class="alert alert-danger mb-0">
                    <h5 class="fw-bold">
                        Proyecto no aprobado
                    </h5>

                    Tu nota final es inferior a 3.0.
                    Puedes consultar las observaciones
                    con tu profesor director.
                </div>

            @endif

        </div>
    </div>

@elseif($proyecto->fase_evaluacion === 'finalizado')

    <div class="alert alert-warning mt-4">
        El proyecto está finalizado, pero todavía no
        se puede calcular su resultado. Consulta
        con tu profesor director.
    </div>

@endif


@endsection