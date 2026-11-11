@extends('layouts.app')
@section('title','Panel administrador')
@section('content')
<section class="unab-hero mb-4"><div class="unab-kicker mb-2">PORTAL DE ADMINISTRADOR</div><h1 class="fw-bold">¡Bienvenido, {{ auth()->user()->name }}!</h1><p class="mb-0">Gestiona los usuarios y sus permisos desde un solo lugar.</p></section>
<div class="row g-4"><div class="col-md-6 col-xl-4"><div class="unab-card h-100"><div class="card-body"><div class="unab-stat-icon mb-3"><i class="bi bi-people"></i></div><h4 class="fw-bold">Gestión del administrador</h4><p class="text-secondary">Gestiona los usuarios y sus permisos desde un solo lugar.</p><a href="{{ route('admin.usuarios.index') }}" class="btn btn-primary">Administrar usuarios <i class="bi bi-arrow-right ms-1"></i></a></div></div></div></div>
@endsection
