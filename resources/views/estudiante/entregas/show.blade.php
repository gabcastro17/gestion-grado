
@extends('layouts.app')

@section('content')

@php
    // Comprobar si la entrega normal está dentro de las fechas.
    $dentroDeFechas =
        (!$entrega->fecha_inicio ||
            now()->gte($entrega->fecha_inicio->copy()->startOfDay()))
        &&
        (!$entrega->fecha_limite ||
            now()->lte($entrega->fecha_limite->copy()->endOfDay()));

    // Comprobar si existe un plazo de corrección vigente.
    $puedeCorregir =
        $proyecto->fase_evaluacion === 'correcciones'
        && $proyecto->fecha_limite_correccion
        && now()->lte(
            $proyecto->fecha_limite_correccion->copy()->endOfDay()
        );

    // Bloquear nuevas entregas después de aprobar evaluación 1.
    $faseBloqueada = in_array(
        $proyecto->fase_evaluacion,
        ['pendiente_jurados', 'evaluacion_2', 'finalizado'],
        true
    );

    $puedeEnviar =
        $proyecto->estado === 'activo'
        && !$faseBloqueada
        && (
            $puedeCorregir
            || (
                $proyecto->fase_evaluacion !== 'correcciones'
                && $dentroDeFechas
            )
        );
@endphp

<a href="{{ route('estudiante.proyectos.show', $proyecto) }}"
   class="btn btn-outline-secondary mb-4">
    Volver a mi proyecto
</a>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">

        <h2 class="fw-bold">{{ $entrega->titulo }}</h2>

        <span class="badge bg-secondary mb-3">
            {{ ucfirst($entrega->estado) }}
        </span>

        <h5>Instrucciones del profesor</h5>

        <p style="white-space: pre-line;">
            {{ $entrega->descripcion ?: 'Sin instrucciones adicionales.' }}
        </p>

        <p>
            <strong>Fecha límite original:</strong>
            {{ $entrega->fecha_limite?->format('d/m/Y') ?? 'Sin definir' }}
        </p>

        @if($proyecto->fase_evaluacion === 'correcciones')
            <div class="alert alert-warning mt-3">
                <h5 class="fw-bold">Correcciones solicitadas</h5>

                <p class="mb-2">
                    El director ha solicitado correcciones
                    al proyecto.
                </p>

                <strong>Nuevo plazo:</strong>
                {{ $proyecto->fecha_limite_correccion?->format('d/m/Y') ?? 'Sin definir' }}

                @if(!$puedeCorregir)
                    <p class="mb-0 mt-2 text-danger">
                        El plazo para presentar correcciones
                        ha vencido.
                    </p>
                @endif
            </div>
        @endif

        @if($faseBloqueada)
            <div class="alert alert-success mt-3">
                El proyecto ya superó la evaluación del director
                y pasó a la siguiente fase.
            </div>
        @endif

    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">

        <h4 class="fw-bold mb-3">
            {{ $puedeCorregir ? 'Presentar corrección' : 'Presentar documento' }}
        </h4>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($puedeEnviar)

            <form
                action="{{ route('estudiante.documentos.store', [$proyecto, $entrega]) }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Subir PDF o Word
                    </label>

                    <input
                        type="file"
                        name="archivo"
                        class="form-control"
                        accept=".pdf,.doc,.docx"
                    >

                    <small class="text-muted">
                        Tamaño máximo: 10 MB.
                    </small>
                </div>

                <p class="text-center text-muted">O</p>

                <div class="mb-3">
                    <label class="form-label">
                        Enlace del documento
                    </label>

                    <input
                        type="url"
                        name="enlace"
                        class="form-control"
                        placeholder="https://..."
                        value="{{ old('enlace') }}"
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ $puedeCorregir ? 'Enviar corrección' : 'Enviar entrega' }}
                </button>
            </form>

        @else

            <div class="alert alert-warning">
                @if($faseBloqueada)
                    Este proyecto ya pasó a la siguiente fase
                    y no admite nuevos documentos.
                @elseif($proyecto->fase_evaluacion === 'correcciones')
                    No hay un plazo de corrección vigente.
                @else
                    Esta entrega no está habilitada para recibir documentos.
                @endif
            </div>

        @endif

    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <h4 class="fw-bold mb-3">Mis documentos enviados</h4>

        @forelse($entrega->documentos as $documento)

            <div class="border rounded p-3 mb-3">

                <strong>{{ $documento->nombre_archivo }}</strong>

                <p class="text-muted small mb-2">
                    Enviado:
                    {{ $documento->created_at->format('d/m/Y H:i') }}
                </p>

                @if($documento->ruta_archivo)

                    <a
                        href="{{ route('documentos.descargar', $documento) }}"
                        class="btn btn-outline-primary btn-sm"
                    >
                        Descargar
                    </a>

                @elseif($documento->enlace)

                    <a
                        href="{{ $documento->enlace }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-outline-primary btn-sm"
                    >
                        Abrir enlace
                    </a>

                @endif

            </div>

        @empty

            <p class="text-muted">
                Todavía no has enviado documentos.
            </p>

        @endforelse

    </div>
</div>

@endsection
