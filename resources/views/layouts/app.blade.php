<!DOCTYPE html>
<html lang="es">
<head>
 <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
 <title>@yield('title', 'Gestión de Grado') | UNAB</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
 <link href="{{ asset('css/unab.css') }}" rel="stylesheet">
</head>
<body>
<header class="unab-topbar"><div class="container-fluid px-3 px-lg-5 py-3 d-flex align-items-center justify-content-between gap-3">
 <a class="unab-brand" href="{{ auth()->check() ? route('panel') : route('login') }}"><span class="unab-mark">U</span><span>Gestión de Grado<small>Plataforma académica · Estilo UNAB</small></span></a>
 @auth <div class="d-flex align-items-center gap-3"><span class="d-none d-sm-inline text-secondary small"><i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}</span><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-outline-primary btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Salir</button></form></div> @endauth
</div></header>
@auth
<div class="unab-shell"><aside class="unab-sidebar"><div class="menu-label">NAVEGACIÓN</div>
 <a href="{{ route('panel') }}" class="{{ request()->routeIs('panel') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i>Mi panel</a>
 @if(auth()->user()->role === 'administrador')
 <a href="{{ route('admin.usuarios.index') }}" class="{{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}"><i class="bi bi-people"></i>Usuarios</a>
 @elseif(auth()->user()->role === 'profesor')
 <a href="{{ route('profesor.propuestas.index') }}" class="{{ request()->routeIs('profesor.propuestas.*') ? 'active' : '' }}"><i class="bi bi-journal-text"></i>Mis propuestas</a>
 @elseif(auth()->user()->role === 'estudiante')
 <a href="{{ route('estudiante.propuestas.index') }}" class="{{ request()->routeIs('estudiante.propuestas.*') ? 'active' : '' }}"><i class="bi bi-search"></i>Banco de propuestas</a>
 @endif
 </aside><main class="unab-main">
@else
<main class="unab-guest">
@endauth
<div class="w-100" style="max-width:1280px;margin:auto">
 @if(session('success'))<div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>@endif
 @if($errors->any())<div class="alert alert-danger border-0 shadow-sm"><strong>Revisa la información:</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
 @yield('content')
 <footer class="unab-footer">Gestión de Grado · Diseño académico inspirado en la UNAB · {{ date('Y') }}</footer>
</div>
@auth </main></div> @else </main> @endauth
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
