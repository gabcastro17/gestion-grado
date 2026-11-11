@extends('layouts.app')

@section('content')

<a href="{{ route('profesor.proyectos.show', $proyecto) }}"
   class="btn btn-outline-secondary mb-4">
    Volver al proyecto
</a>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">

        <h2 class="fw-bold">{{ $entrega->titulo }}</h2>

        <span class="badge bg-secondary">
            {{ ucfirst($entrega->estado) }}
        </span>

        <hr>

        <h5>Instrucciones</h5>

        <p style="white-space: pre-line;">{{ $entrega->descripcion ?: 'Sin instrucciones adicionales.' }}</p>

        <p>
            <strong>Fecha de inicio:</strong>
            {{ $entrega->fecha_inicio?->format('d/m/Y') ?? 'Sin definir' }}
        </p>

        <p>
            <strong>Fecha límite:</strong>
            {{ $entrega->fecha_limite?->format('d/m/Y') ?? 'Sin definir' }}
        </p>

    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <h4 class="fw-bold mb-3">Documentos recibidos</h4>

        @forelse($entrega->documentos as $documento)

            <div class="border rounded p-3 mb-3">

                <h5>{{ $documento->nombre_archivo }}</h5>

                <p class="text-muted mb-2">
                    Estudiante: {{ $documento->estudiante->name }}
                </p>

                <p class="text-muted small">
                    Enviado:
                    {{ $documento->created_at->format('d/m/Y H:i') }}
                </p>

                @if($documento->ruta_archivo)
                    <a href="{{ route('documentos.descargar', $documento) }}"
                       class="btn btn-primary btn-sm">
                        Descargar documento
                    </a>
                @elseif($documento->enlace)
                    <a href="{{ $documento->enlace }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="btn btn-outline-primary btn-sm">
                        Abrir enlace
                    </a>
                @endif

            </div>

        @empty

            <div class="alert alert-info">
                El estudiante todavía no ha enviado documentos.
            </div>

        @endforelse

    </div>
</div>

@endsection