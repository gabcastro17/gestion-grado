<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function index(Request $request)
    {
        $proyectos = Proyecto::with(['estudiante', 'propuesta'])
            ->where('profesor_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('profesor.proyectos.index', compact('proyectos'));
    }

    public function show(Request $request, Proyecto $proyecto)
    {
        abort_unless(
            (int) $proyecto->profesor_id === (int) $request->user()->id,
            403,
            'No tienes permiso para consultar este proyecto.'
        );

        $proyecto->load(['estudiante', 'propuesta', 'entregas']);

        return view('profesor.proyectos.show', compact('proyecto'));
    }
}