@extends('layouts.app')
@section('title','Nueva propuesta')
@section('content')
<div class="mb-4"><div class="unab-kicker">DOCENTES / PROPUESTAS</div><h1 class="fw-bold">Nueva propuesta</h1><p class="text-secondary">Describe el alcance académico y los objetivos de tu idea.</p></div>
<div class="unab-card unab-accent" style="max-width:920px"><div class="card-body"><form method="POST" action="{{ route('profesor.propuestas.store') }}">@csrf 
<div class="mb-3"><label class="form-label" for="titulo">Título</label><input class="form-control" id="titulo" name="titulo" value="{{ old('titulo') }}" maxlength="255" required></div><div class="mb-3"><label class="form-label" for="descripcion">Descripción</label><textarea class="form-control" id="descripcion" name="descripcion" rows="5" required>{{ old('descripcion') }}</textarea></div><div class="mb-3"><label class="form-label" for="introduccion">Introducción</label><textarea class="form-control" id="introduccion" name="introduccion" rows="4" >{{ old('introduccion') }}</textarea></div><div class="mb-3"><label class="form-label" for="objetivos">Objetivos</label><textarea class="form-control" id="objetivos" name="objetivos" rows="4" >{{ old('objetivos') }}</textarea></div>
<div class="d-flex flex-wrap gap-2"><button class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar borrador</button><a class="btn btn-outline-primary" href="{{ route('profesor.propuestas.index') }}">Cancelar</a></div></form></div></div>
@endsection
