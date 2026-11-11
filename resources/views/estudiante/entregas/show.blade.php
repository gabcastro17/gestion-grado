@extends('layouts.app')

@section('content')

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

        <p style="white-space: pre-line;">{{ $entrega->descripcion ?: 'Sin instrucciones adicionales.' }}</p>

        <p>
            <strong>Fecha límite:</strong>
            {{ $entrega->fecha_limite?->format('d/m/Y') ?? 'Sin definir' }}
        </p>

    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">

        <h4 class="fw-bold mb-3">Presentar documento</h4>

        @if(
            $proyecto->estado === 'activo' &&
            (!$entrega->fecha_inicio || now()->gte($entrega->fecha_inicio->startOfDay())) &&
            (!$entrega->fecha_limite || now()->lte($entrega->fecha_limite->endOfDay()))
        )

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

                    <input type="file"
                           name="archivo"
                           class="form-control"
                           accept=".pdf,.doc,.docx">

                    <small class="text-muted">
                        Tamaño máximo: 10 MB.
                    </small>
                </div>

                <p class="text-center text-muted">O</p>

                <div class="mb-3">
                    <label class="form-label">
                        Enlace del documento
                    </label>

                    <input type="url"
                           name="enlace"
                           class="form-control"
                           placeholder="https://..."
                           value="{{ old('enlace') }}">
                </div>

                <button type="submit" class="btn btn-primary">
                    Enviar entrega
                </button>

            </form>

        @else

            <div class="alert alert-warning">
                Esta entrega no está habilitada para recibir documentos.
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

                    <a href="{{ route('documentos.descargar', $documento) }}"
                       class="btn btn-outline-primary btn-sm">
                        Descargar
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

            <p class="text-muted">
                Todavía no has enviado documentos.
            </p>

        @endforelse

    </div>
</div>

@endsection