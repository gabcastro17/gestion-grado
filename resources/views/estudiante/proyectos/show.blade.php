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

@endsection