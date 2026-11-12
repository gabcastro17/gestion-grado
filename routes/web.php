<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PinLoginController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Profesor\PropuestaController;
use App\Http\Controllers\Estudiante\BancoPropuestasController;
use App\Http\Controllers\Estudiante\SolicitudController as SolicitudEstudianteController;
use App\Http\Controllers\Profesor\SolicitudController as SolicitudProfesorController;
use App\Http\Controllers\Estudiante\ProyectoController as ProyectoEstudianteController;
use App\Http\Controllers\Profesor\ProyectoController as ProyectoProfesorController;
use App\Http\Controllers\DocumentoDescargaController;
use App\Http\Controllers\Profesor\EntregaController;
use App\Http\Controllers\Estudiante\DocumentoController;
use App\Http\Controllers\Profesor\EvaluacionDirectorController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {

    Route::get('/login', [
        PinLoginController::class,
        'mostrarLogin'
    ])->name('login');

    Route::post('/enviar-pin', [
        PinLoginController::class,
        'enviarPin'
    ])->name('pin.enviar');

    Route::get('/verificar-pin', [
        PinLoginController::class,
        'mostrarFormularioPin'
    ])->name('pin.formulario');

    Route::post('/verificar-pin', [
        PinLoginController::class,
        'verificarPin'
    ])->name('pin.verificar');

});

Route::middleware('auth')->group(function () {

    Route::get('/panel', [
        PanelController::class,
        'index'
    ])->name('panel');

    Route::post('/logout', [
        PinLoginController::class,
        'cerrarSesion'
    ])->name('logout');

    // Rutas exclusivas del administrador
Route::middleware(['auth', 'administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/usuarios', [
            UsuarioController::class,
            'index'
        ])->name('usuarios.index');

        Route::get('/usuarios/crear', [
            UsuarioController::class,
            'create'
        ])->name('usuarios.create');

        Route::post('/usuarios', [
            UsuarioController::class,
            'store'
        ])->name('usuarios.store');

        Route::get('/usuarios/{usuario}/editar', [
            UsuarioController::class,
            'edit'
        ])->name('usuarios.edit');

        Route::put('/usuarios/{usuario}', [
            UsuarioController::class,
            'update'
        ])->name('usuarios.update');

        Route::patch('/usuarios/{usuario}/estado', [
            UsuarioController::class,
            'cambiarEstado'
        ])->name('usuarios.estado');


    });


});
// rutas del profesor
Route::middleware(['auth', 'rol:profesor'])
    ->prefix('profesor')
    ->name('profesor.')
    ->group(function () {

        Route::get('/propuestas', [
            PropuestaController::class, 'index'
        ])->name('propuestas.index');

        Route::get('/propuestas/crear', [
            PropuestaController::class, 'create'
        ])->name('propuestas.create');

        Route::post('/propuestas', [
            PropuestaController::class, 'store'
        ])->name('propuestas.store');

        Route::get('/propuestas/{propuesta}/editar', [
            PropuestaController::class, 'edit'
        ])->name('propuestas.edit');

        Route::put('/propuestas/{propuesta}', [
            PropuestaController::class, 'update'
        ])->name('propuestas.update');

        Route::patch('/propuestas/{propuesta}/publicar', [
            PropuestaController::class, 'publicar'
        ])->name('propuestas.publicar');

        Route::patch('/propuestas/{propuesta}/cerrar', [
            PropuestaController::class, 'cerrar'
        ])->name('propuestas.cerrar');
    });

// rutas del estudiante
Route::middleware(['auth', 'rol:estudiante'])
    ->prefix('estudiante')
    ->name('estudiante.')
    ->group(function () {

        Route::get('/propuestas', [
            BancoPropuestasController::class, 'index'
        ])->name('propuestas.index');

        Route::get('/propuestas/{propuesta}', [
            BancoPropuestasController::class, 'show'
        ])->name('propuestas.show');
    });

// solicitudes del estudiante
Route::middleware(['auth', 'rol:estudiante'])
    ->prefix('estudiante')
    ->name('estudiante.')
    ->group(function () {

        Route::get('/solicitudes', [
            SolicitudEstudianteController::class,
            'index'
        ])->name('solicitudes.index');

        Route::post('/propuestas/{propuesta}/solicitar', [
            SolicitudEstudianteController::class,
            'store'
        ])->name('solicitudes.store');
    });

// solicitudes del profesor
Route::middleware(['auth', 'rol:profesor'])
    ->prefix('profesor')
    ->name('profesor.')
    ->group(function () {

        Route::get('/solicitudes', [
            SolicitudProfesorController::class,
            'index'
        ])->name('solicitudes.index');

        Route::patch('/solicitudes/{solicitud}/aceptar', [
            SolicitudProfesorController::class,
            'aceptar'
        ])->name('solicitudes.aceptar');

        Route::patch('/solicitudes/{solicitud}/rechazar', [
            SolicitudProfesorController::class,
            'rechazar'
        ])->name('solicitudes.rechazar');
    });

// proyectos del estudiante
Route::middleware(['auth', 'rol:estudiante'])
    ->prefix('estudiante')
    ->name('estudiante.')
    ->group(function () {

        Route::get('/proyectos', [
            ProyectoEstudianteController::class,
            'index'
        ])->name('proyectos.index');

        Route::get('/proyectos/{proyecto}', [
            ProyectoEstudianteController::class,
            'show'
        ])->name('proyectos.show');
    });

// proyectos del profesor
Route::middleware(['auth', 'rol:profesor'])
    ->prefix('profesor')
    ->name('profesor.')
    ->group(function () {

        Route::get('/proyectos', [
            ProyectoProfesorController::class,
            'index'
        ])->name('proyectos.index');

        Route::get('/proyectos/{proyecto}', [
            ProyectoProfesorController::class,
            'show'
        ])->name('proyectos.show');

        Route::post('/proyectos/{proyecto}/evaluacion-director', [
            EvaluacionDirectorController::class, 
            'store'
        ])->name('evaluacion-director.store');
    });

// descargar documentos de forma segura
Route::middleware(['auth'])
    ->get('/documentos/{documento}/descargar', [
        DocumentoDescargaController::class,
        'descargar'
    ])
    ->name('documentos.descargar');

// entregas del profesor
Route::middleware(['auth', 'rol:profesor'])
    ->prefix('profesor')
    ->name('profesor.')
    ->group(function () {

        Route::get('/proyectos/{proyecto}/entregas/crear', [
            EntregaController::class, 'create'
        ])->name('entregas.create');

        Route::post('/proyectos/{proyecto}/entregas', [
            EntregaController::class, 'store'
        ])->name('entregas.store');

        Route::get('/proyectos/{proyecto}/entregas/{entrega}', [
            EntregaController::class, 'show'
        ])->name('entregas.show');
        
        Route::get('/jurados', [
            \App\Http\Controllers\Profesor\InvitacionJuradoController::class,
            'index',
        ])->name('jurados.index');

        Route::post('/jurados/{jurado}/aceptar', [
            \App\Http\Controllers\Profesor\InvitacionJuradoController::class,
            'aceptar',
        ])->name('jurados.aceptar');

        Route::post('/jurados/{jurado}/rechazar', [
            \App\Http\Controllers\Profesor\InvitacionJuradoController::class,
            'rechazar',
        ])->name('jurados.rechazar');

        Route::post('/jurados/{jurado}/calificar', [
            \App\Http\Controllers\Profesor\CalificacionJuradoController::class,
            'store',
        ])->name('jurados.calificar');
    });

// entregas y documentos del estudiante
Route::middleware(['auth', 'rol:estudiante'])
    ->prefix('estudiante')
    ->name('estudiante.')
    ->group(function () {

        Route::get('/proyectos/{proyecto}/entregas/{entrega}', [
            DocumentoController::class, 'show'
        ])->name('entregas.show');

        Route::post('/proyectos/{proyecto}/entregas/{entrega}/documentos', [
            DocumentoController::class, 'store'
        ])->name('documentos.store');
    });

// descarga privada de documentos
Route::middleware('auth')
    ->get('/documentos/{documento}/descargar', [
        DocumentoDescargaController::class, 'descargar'
    ])
    ->name('documentos.descargar');

Route::middleware('auth')->group(function () {
    Route::get(
        '/telegram/vincular',
        [\App\Http\Controllers\TelegramVinculacionController::class, 'mostrar']
    )->name('telegram.vincular');

    Route::post(
        '/telegram/generar-codigo',
        [\App\Http\Controllers\TelegramVinculacionController::class, 'generar']
    )->middleware('throttle:5,1')
     ->name('telegram.generar-codigo');
});


