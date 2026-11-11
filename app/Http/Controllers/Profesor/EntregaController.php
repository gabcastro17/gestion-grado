<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Entrega;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

//Este controlador permite que el profesor cree entregas únicamente en sus proyectos activos.

class EntregaController extends Controller
{
    public function create(Request $request, Proyecto $proyecto)
    {
        $this->autorizar($request, $proyecto);

        abort_unless($proyecto->estado === 'activo', 403);

        return view('profesor.entregas.create', compact('proyecto'));
    }

    public function store(Request $request, Proyecto $proyecto)
    {
        $this->autorizar($request, $proyecto);

        abort_unless($proyecto->estado === 'activo', 403);

        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:10000'],
            'numero_orden' => ['required', 'integer', 'min:1'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_limite' => ['required', 'date', 'after_or_equal:fecha_inicio'], //también comprueba que la fecha límite no sea anterior a la fecha de inicio
        ]);

        if (Entrega::where('proyecto_id', $proyecto->id)
            ->where('numero_orden', $datos['numero_orden'])
            ->exists()) {
            throw ValidationException::withMessages([
                'numero_orden' => 'Ya existe una entrega con ese número.',
            ]);
        }

        $proyecto->entregas()->create([
            ...$datos,
            'estado' => 'pendiente',
        ]);

        return redirect()
            ->route('profesor.proyectos.show', $proyecto)
            ->with('success', 'Entrega creada correctamente.');
    }

    public function show(Request $request, Proyecto $proyecto, Entrega $entrega)
    {
        $this->autorizar($request, $proyecto);

        abort_unless(
            (int) $entrega->proyecto_id === (int) $proyecto->id,
            404
        );

        $entrega->load(['documentos.estudiante', 'retroalimentaciones']);

        return view('profesor.entregas.show', compact(
            'proyecto',
            'entrega'
        ));
    }

    private function autorizar(Request $request, Proyecto $proyecto): void
    {
        abort_unless(
            (int) $proyecto->profesor_id === (int) $request->user()->id,
            403
        );
    }
}