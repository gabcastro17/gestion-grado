@extends('layouts.app')
@section('title','Panel estudiante')
@section('content')
<section class="unab-hero mb-4">
    <div class="unab-kicker mb-2">PORTAL DE ESTUDIANTE</div>
    <h1 class="fw-bold">¡Bienvenido, {{ auth()->user()->name }}!</h1>
    <p class="mb-0">Descubre propuestas académicas y encuentra tu próximo proyecto.</p>
</section>
<div class="row g-4">
    <div class="col-md-6 col-xl-4">
        <div class="unab-card h-100">
            <div class="card-body">
                <div class="unab-stat-icon mb-3">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <h4 class="fw-bold">Banco de propuestas</h4>
                <p class="text-secondary">Descubre propuestas académicas y encuentra tu próximo proyecto.</p>
                <a href="{{ route('estudiante.solicitudes.index') }}"
                    class="btn btn-outline-primary">
                        Mis solicitudes
                </a>
                <a href="{{ route('estudiante.proyectos.index') }}"
                    class="btn btn-primary">
                        Mis proyectos
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
