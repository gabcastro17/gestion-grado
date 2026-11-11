@extends('layouts.app')

@section('content')

<div class="mb-4">
    <h2 class="fw-bold">Mis proyectos de grado</h2>
    <p class="text-muted">
        Consulta los proyectos que tienes asignados.
    </p>
</div>

<div class="row g-4">

    @forelse($proyectos as $proyecto)

        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">

                    <span class="badge bg-success mb-3">
                        {{ ucfirst($proyecto->estado) }}
                    </span>

                    <h4 class="fw-bold">
                        {{ $proyecto->propuesta->titulo }}
                    </h4>

                    <p class="text-muted">
                        Director:
                        {{ $proyecto->profesor->name }}
                    </p>

                    <p>
                        <strong>Fecha de inicio:</strong>
                        {{ $proyecto->fecha_inicio?->format('d/m/Y') ?? 'Sin definir' }}
                    </p>

                    <a
                        href="{{ route('estudiante.proyectos.show', $proyecto) }}"
                        class="btn btn-primary"
                    >
                        Ver mi proyecto
                    </a>

                </div>
            </div>
        </div>

    @empty

        <div class="col-12">
            <div class="alert alert-info">
                Todavía no tienes proyectos asignados.
                Explora el banco de propuestas para solicitar uno.
            </div>

            <a href="{{ route('estudiante.propuestas.index') }}"
               class="btn btn-primary">
                Explorar propuestas
            </a>
        </div>

    @endforelse

</div>

<div class="mt-4">
    {{ $proyectos->links('pagination::bootstrap-5') }}
</div>

@endsection