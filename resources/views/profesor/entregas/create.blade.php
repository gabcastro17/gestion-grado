@extends('layouts.app')

@section('content')

<a href="{{ route('profesor.proyectos.show', $proyecto) }}"
   class="btn btn-outline-secondary mb-4">
    Volver al proyecto
</a>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <h2 class="fw-bold mb-2">Nueva entrega</h2>

        <p class="text-muted mb-4">
            Proyecto: {{ $proyecto->propuesta->titulo }}
        </p>

        <form action="{{ route('profesor.entregas.store', $proyecto) }}"
              method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Título de la entrega</label>

                <input type="text"
                       name="titulo"
                       class="form-control"
                       value="{{ old('titulo') }}"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Instrucciones</label>

                <textarea name="descripcion"
                          class="form-control"
                          rows="5"
                          placeholder="Describe lo que debe presentar el estudiante...">{{ old('descripcion') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Número de entrega</label>

                <input type="number"
                       name="numero_orden"
                       class="form-control"
                       min="1"
                       value="{{ old('numero_orden', 1) }}"
                       required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha de inicio</label>

                    <input type="date"
                           name="fecha_inicio"
                           class="form-control"
                           value="{{ old('fecha_inicio') }}"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha límite</label>

                    <input type="date"
                           name="fecha_limite"
                           class="form-control"
                           value="{{ old('fecha_limite') }}"
                           required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                Crear entrega
            </button>

        </form>

    </div>
</div>

@endsection