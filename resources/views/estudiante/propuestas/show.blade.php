@extends('layouts.app')
@section('title','Detalle de propuesta')
@section('content')
<a class="text-decoration-none d-inline-block mb-3" href="{{ route('estudiante.propuestas.index') }}"><i class="bi bi-arrow-left me-1"></i>Volver al banco</a>
<div class="unab-card unab-accent">
    <div class="card-body p-4 p-lg-5">
        <div class="unab-kicker mb-2">PROPUESTA ACADÉMICA</div>
        <span class="badge badge-publicada mb-3">Publicada</span>
        <h1 class="fw-bold mb-3">{{ $propuesta->titulo }}</h1>
        <p class="text-secondary mb-4">
            <i class="bi bi-person-badge me-1"></i>Docente responsable: <strong>{{ $propuesta->profesor->name }}</strong>
        </p>
        <hr class="mb-4">
        <h4 class="fw-bold">Descripción</h4>
        <p class="unab-description mb-4">{{ $propuesta->descripcion }}</p>
        <h4 class="fw-bold">Introducción</h4>
        <p class="unab-description mb-4">{{ $propuesta->introduccion ?: 'No registrada.' }}</p>
        <h4 class="fw-bold">Objetivos</h4>
        <p class="unab-description mb-4">{{ $propuesta->objetivos ?: 'No registrados.' }}</p>
        <div class="mt-4">

    <h5 class="fw-bold">¿Te interesa esta propuesta?</h5>

    <p class="text-muted">
        Envía una solicitud al profesor para desarrollar
        este proyecto de grado.
    </p>

    <form
        action="{{ route('estudiante.solicitudes.store', $propuesta) }}"
        method="POST"
    >
        @csrf

        <div class="mb-3">
            <label class="form-label">
                Mensaje para el profesor (opcional)
            </label>

            <textarea
                name="mensaje"
                class="form-control"
                rows="4"
                maxlength="2000"
                placeholder="Explica brevemente por qué te interesa esta propuesta..."
            >{{ old('mensaje') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            Solicitar propuesta
        </button>

        <a href="{{ route('estudiante.propuestas.index') }}"
           class="btn btn-outline-secondary">
            Volver
        </a>
    </form>

</div>
    </div>
</div>
@endsection
