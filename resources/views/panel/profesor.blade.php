@extends('layouts.app')
@section('title','Panel profesor')
@section('content')
<section class="unab-hero mb-4">
    <div class="unab-kicker mb-2">PORTAL DE PROFESOR</div>
    <h1 class="fw-bold">¡Bienvenido, {{ auth()->user()->name }}!</h1>
    <p class="mb-0">Publica propuestas y acompaña a los estudiantes en sus proyectos.</p>
</section>
<div class="row g-4">
    <div class="col-md-6 col-xl-4">
        <div class="unab-card h-100">
            <div class="card-body">
                <div class="unab-stat-icon mb-3">
                    <i class="bi bi-journal-text"></i>
                </div>
                <h4 class="fw-bold">Mis propuestas</h4>
                <p class="text-secondary">Publica propuestas y acompaña a los estudiantes en sus proyectos.</p>
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
@endsection
