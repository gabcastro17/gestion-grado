@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Mis solicitudes</h2>
        <p class="text-muted">
            Consulta el estado de tus solicitudes de grado.
        </p>
    </div>

    <a href="{{ route('estudiante.propuestas.index') }}"
       class="btn btn-primary">
        Explorar propuestas
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Propuesta</th>
                        <th>Profesor</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($solicitudes as $solicitud)
                        <tr>
                            <td>
                                <strong>
                                    {{ $solicitud->propuesta->titulo }}
                                </strong>
                            </td>

                            <td>
                                {{ $solicitud->propuesta->profesor->name }}
                            </td>

                            <td>
                                {{ $solicitud->fecha_solicitud?->format('d/m/Y') }}
                            </td>

                            <td>
                                @if($solicitud->estado === 'pendiente')
                                    <span class="badge bg-warning text-dark">
                                        Pendiente
                                    </span>
                                @elseif($solicitud->estado === 'aceptada')
                                    <span class="badge bg-success">
                                        Aceptada
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        Rechazada
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                Todavía no has enviado solicitudes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $solicitudes->links('pagination::bootstrap-5') }}

    </div>
</div>

@endsection