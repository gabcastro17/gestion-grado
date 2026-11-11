@extends('layouts.app')

@section('content')

<div class="mb-4">
    <h2 class="fw-bold">Proyectos que dirijo</h2>
    <p class="text-muted">
        Consulta los proyectos de grado bajo tu dirección.
    </p>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table align-middle">

                <thead>
                    <tr>
                        <th>Proyecto</th>
                        <th>Estudiante</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($proyectos as $proyecto)

                        <tr>
                            <td>
                                <strong>
                                    {{ $proyecto->propuesta->titulo }}
                                </strong>
                            </td>

                            <td>
                                {{ $proyecto->estudiante->name }}
                            </td>

                            <td>
                                <span class="badge bg-success">
                                    {{ ucfirst($proyecto->estado) }}
                                </span>
                            </td>

                            <td>
                                <a
                                    href="{{ route('profesor.proyectos.show', $proyecto) }}"
                                    class="btn btn-primary btn-sm"
                                >
                                    Ver proyecto
                                </a>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="text-center py-4">
                                Todavía no diriges proyectos.
                            </td>
                        </tr>

                    @endforelse
                </tbody>

            </table>
        </div>

        {{ $proyectos->links('pagination::bootstrap-5') }}

    </div>
</div>

@endsection