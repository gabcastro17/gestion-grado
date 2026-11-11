<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use Illuminate\Http\Request;

//este controlador garantiza que un estudiante solo pueda consultar sus propios proyectos
class ProyectoController extends Controller
{
    public function index(Request $request)
    {
        $proyectos = Proyecto::with(['propuesta', 'profesor'])
            ->where('estudiante_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('estudiante.proyectos.index', compact('proyectos'));
    }

    public function show(Request $request, Proyecto $proyecto)
    {
        abort_unless(
            (int) $proyecto->estudiante_id === (int) $request->user()->id,
            403,
            'No tienes permiso para consultar este proyecto.'
        );

        $proyecto->load(['propuesta', 'profesor', 'entregas']);

        return view('estudiante.proyectos.show', compact('proyecto'));
    }
}