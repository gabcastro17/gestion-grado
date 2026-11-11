@extends('layouts.app')
@section('title','Registrar usuario')
@section('content')
<div class="mb-4"><div class="unab-kicker">ADMINISTRACIÓN / USUARIOS</div><h1 class="fw-bold">Registrar usuario</h1><p class="text-secondary">Completa los datos de la cuenta académica.</p></div>
<div class="unab-card unab-accent" style="max-width:780px"><div class="card-body"><form method="POST" action="{{ route('admin.usuarios.store') }}">@csrf 
<div class="mb-3"><label for="name" class="form-label">Nombre completo</label><input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" required maxlength="255"></div>
<div class="mb-3"><label for="email" class="form-label">Correo electrónico</label><input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required maxlength="255"></div>
<div class="mb-4"><label for="role" class="form-label">Rol</label><select id="role" name="role" class="form-select" required><option value="">Selecciona un rol</option><option value="estudiante" @selected(old('role') === 'estudiante')>Estudiante</option><option value="profesor" @selected(old('role') === 'profesor')>Profesor</option></select></div>
<div class="d-flex gap-2"><button class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Registrar usuario</button><a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-primary">Cancelar</a></div></form></div></div>
@endsection
