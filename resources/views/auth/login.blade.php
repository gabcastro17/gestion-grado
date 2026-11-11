@extends('layouts.app')
@section('title','Iniciar sesión')
@section('content')
<div class="row g-0 unab-card overflow-hidden mx-auto" style="max-width:900px">
 <div class="col-lg-5 unab-login-side d-flex flex-column justify-content-center"><div class="unab-kicker mb-3">PORTAL ACADÉMICO</div><h1 class="display-6 fw-bold">Tu proyecto de grado comienza aquí.</h1><div class="unab-divider my-4"></div><p class="mb-0 opacity-75">Un espacio para estudiantes, docentes y administradores. Consulta propuestas y acompaña cada etapa de tu proyecto.</p></div>
 <div class="col-lg-7 p-4 p-md-5"><div class="unab-kicker mb-2">BIENVENIDO</div><h2 class="fw-bold mb-2">Iniciar sesión</h2><p class="text-secondary mb-4">Recibe un código temporal en tu correo registrado.</p>
 <form action="{{ route('pin.enviar') }}" method="POST">@csrf<div class="mb-4"><label class="form-label" for="email">Correo electrónico</label><input id="email" type="email" name="email" class="form-control form-control-lg" placeholder="usuario@correo.edu.co" value="{{ old('email') }}" autocomplete="email" required></div><button class="btn btn-primary btn-lg w-100"><i class="bi bi-envelope me-2"></i>Enviar código PIN</button></form>
 <p class="text-secondary small mt-4 mb-0"><i class="bi bi-shield-check me-1"></i>Acceso mediante código de seis dígitos. No necesitas contraseña.</p></div>
</div>
@endsection
