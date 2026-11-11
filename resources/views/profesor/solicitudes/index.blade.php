@extends('layouts.app')

@section('content')

<div class="mb-4">
    <h2 class="fw-bold">Solicitudes recibidas</h2>
    <p class="text-muted">
        Revisa las solicitudes de estudiantes interesados
        en tus propuestas.
    </p>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Propuesta</th>
                        <th>Mensaje</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($solicitudes as $solicitud)
                        <tr>
                            <td>
                                <strong>
                                    {{ $solicitud->estudiante->name }}
                                </strong>

                                <div class="text-muted small">
                                    {{ $solicitud->estudiante->email }}
                                </div>
                            </td>

                            <td>
                                {{ $solicitud->propuesta->titulo }}
                            </td>

                            <td>
                                {{ $solicitud->mensaje ?: 'Sin mensaje' }}
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

                            <td>
                                @if($solicitud->estado === 'pendiente')

                                    <form
                                        action="{{ route('profesor.solicitudes.aceptar', $solicitud) }}"
                                        method="POST"
                                        class="d-inline"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button class="btn btn-success btn-sm">
                                            Aceptar
                                        </button>
                                    </form>

                                    <form
                                        action="{{ route('profesor.solicitudes.rechazar', $solicitud) }}"
                                        method="POST"
                                        class="d-inline"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button class="btn btn-outline-danger btn-sm">
                                            Rechazar
                                        </button>
                                    </form>

                                @else
                                    <span class="text-muted small">
                                        Respondida
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                No has recibido solicitudes todavía.
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