<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Propuesta;
use Illuminate\Http\Request;

class BancoPropuestasController extends Controller
{
    // Mostrar propuestas publicadas
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $propuestas = Propuesta::with('profesor')
            ->where('estado', 'publicada')
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('titulo', 'like', "%{$buscar}%")
                      ->orWhere('descripcion', 'like', "%{$buscar}%");
                });
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('estudiante.propuestas.index', compact(
            'propuestas',
            'buscar'
        ));
    }

    // Mostrar detalles de una propuesta
    public function show(Propuesta $propuesta)
    {
        abort_unless(
            $propuesta->estado === 'publicada',
            404
        );

        $propuesta->load('profesor');

        return view('estudiante.propuestas.show', compact('propuesta'));
    }
}