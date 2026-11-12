
@extends('layouts.app')

@section('title', 'Panel profesor')

@section('content')

@php
    $invitacionesPendientes = \App\Models\Jurado::query()
        ->where('profesor_id', auth()->id())
        ->where('estado', 'pendiente')
        ->count();
@endphp

<section class="unab-hero mb-4">
    <div class="unab-kicker mb-2">
        PORTAL DE PROFESOR
    </div>

    <h1 class="fw-bold">
        ¡Bienvenido, {{ auth()->user()->name }}!
    </h1>

    <p class="mb-0">
        Publica propuestas, acompaña a los estudiantes
        y participa en la evaluación de proyectos.
    </p>
</section>

<div class="row g-4">

    {{-- TARJETA 1: PROYECTOS Y SOLICITUDES --}}

    <div class="col-md-6 col-xl-4">
        <div class="unab-card h-100">
            <div class="card-body">

                <div class="unab-stat-icon mb-3">
                    <i class="bi bi-journal-text"></i>
                </div>

                <h4 class="fw-bold">
                    Mis propuestas
                </h4>

                <p class="text-secondary">
                    Consulta las solicitudes recibidas
                    y administra los proyectos que diriges.
                </p>

                <div class="d-flex flex-wrap gap-2">

                    <a href="{{ route('profesor.solicitudes.index') }}"
                       class="btn btn-outline-primary">
                        Solicitudes recibidas
                    </a>

                    <a href="{{ route('profesor.proyectos.index') }}"
                       class="btn btn-primary">
                        Proyectos que dirijo
                    </a>

                </div>

            </div>
        </div>
    </div>

    {{-- TARJETA 2: INVITACIONES COMO JURADO --}}

    <div class="col-md-6 col-xl-4">
        <div class="unab-card h-100">
            <div class="card-body">

                <div class="unab-stat-icon mb-3">
                    <i class="bi bi-clipboard-check"></i>
                </div>

                <h4 class="fw-bold">
                    Invitaciones como jurado
                </h4>

                <p class="text-secondary">
                    Consulta las invitaciones para evaluar
                    proyectos de grado, acepta o rechaza
                    solicitudes y registra tus calificaciones.
                </p>

                @if($invitacionesPendientes > 0)

                    <div class="alert alert-warning py-2">
                        Tienes
                        <strong>{{ $invitacionesPendientes }}</strong>
                        {{ $invitacionesPendientes === 1
                            ? 'invitación pendiente'
                            : 'invitaciones pendientes' }}.
                    </div>

                @else

                    <p class="text-muted">
                        No tienes invitaciones pendientes.
                    </p>

                @endif

                <a href="{{ route('profesor.jurados.index') }}"
                   class="btn btn-primary">

                    <i class="bi bi-clipboard-check me-1"></i>

                    Ver mis invitaciones

                </a>

            </div>
        </div>
    </div>

</div>

@endsection
