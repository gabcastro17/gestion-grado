<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Propuesta;
use Illuminate\Http\Request;

class PropuestaController extends Controller
{
    // Listar propuestas del profesor
    public function index(Request $request)
    {
        $propuestas = Propuesta::where(
            'profesor_id',
            $request->user()->id
        )
        ->latest()
        ->paginate(10);

        return view('profesor.propuestas.index', compact('propuestas'));
    }

    // Formulario para crear
    public function create()
    {
        return view('profesor.propuestas.create');
    }

    // Guardar propuesta
    public function store(Request $request)
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'introduccion' => ['nullable', 'string'],
            'objetivos' => ['nullable', 'string'],
        ]);

        Propuesta::create([
            ...$datos,
            'profesor_id' => $request->user()->id,
            'estado' => 'borrador',
        ]);

        return redirect()
            ->route('profesor.propuestas.index')
            ->with('success', 'Propuesta creada correctamente.');
    }

    // Mostrar formulario de edición
    public function edit(Request $request, Propuesta $propuesta)
    {
        $this->verificarPropietario($request, $propuesta);

        return view('profesor.propuestas.edit', compact('propuesta'));
    }

    // Actualizar propuesta
    public function update(Request $request, Propuesta $propuesta)
    {
        $this->verificarPropietario($request, $propuesta);

        if ($propuesta->estado !== 'borrador') {
            return back()->withErrors([
                'propuesta' => 'Solo puedes editar propuestas en borrador.',
            ]);
        }

        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'introduccion' => ['nullable', 'string'],
            'objetivos' => ['nullable', 'string'],
        ]);

        $propuesta->update($datos);

        return redirect()
            ->route('profesor.propuestas.index')
            ->with('success', 'Propuesta actualizada.');
    }

    // Publicar propuesta
    public function publicar(Request $request, Propuesta $propuesta)
    {
        $this->verificarPropietario($request, $propuesta);

        if ($propuesta->estado !== 'borrador') {
            return back()->withErrors([
                'propuesta' => 'Solo se pueden publicar propuestas en borrador.',
            ]);
        }

        $propuesta->update([
            'estado' => 'publicada',
        ]);

        return back()->with('success', 'Propuesta publicada correctamente.');
    }

    // Cerrar propuesta
    public function cerrar(Request $request, Propuesta $propuesta)
    {
        $this->verificarPropietario($request, $propuesta);

        if ($propuesta->estado !== 'publicada') {
            return back()->withErrors([
                'propuesta' => 'Solo puedes cerrar propuestas publicadas.',
            ]);
        }

        $propuesta->update([
            'estado' => 'cerrada',
        ]);

        return back()->with('success', 'Propuesta cerrada.');
    }

    // Verificar que la propuesta pertenezca al profesor
    private function verificarPropietario(
        Request $request,
        Propuesta $propuesta
    ): void {
        abort_unless(
            $propuesta->profesor_id === $request->user()->id,
            403,
            'No puedes modificar esta propuesta.'
        );
    }
}