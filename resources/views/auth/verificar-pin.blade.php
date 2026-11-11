@extends('layouts.app')
@section('title','Verificar código')
@section('content')
<div class="unab-card unab-accent mx-auto" style="max-width:490px">
    <div class="card-body p-4 p-md-5 text-center">
        <div class="unab-stat-icon mx-auto mb-3">
            <i class="bi bi-shield-lock"></i>
        </div>
        <div class="unab-kicker mb-2">VERIFICACIÓN SEGURA</div>
        <h2 class="fw-bold">Ingresa tu PIN</h2>
        <p class="text-secondary mb-4">Revisa el código de seis dígitos enviado a tu correo. Vence en cinco minutos.</p>
        <form action="{{ route('pin.verificar') }}" method="POST">@csrf<input type="hidden" name="email" value="{{ old('email', $email) }}">
            <label class="form-label" for="pin">Código de acceso</label>
            <input id="pin" type="text" name="pin" class="form-control form-control-lg text-center mb-4" style="letter-spacing:.4em;font-weight:800" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus>
            <button class="btn btn-primary btn-lg w-100">Verificar e ingresar</button>
        </form>
        <a class="d-inline-block mt-4" href="{{ route('login') }}">Solicitar otro código</a>
    </div>
</div>
@endsection
