<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\AsignacionJuradosService;

//protege la evaluacion para que solo la pueda hacer el profesor director
//evita que se registren dos evaluaciones simultaneas del mismo intento
class EvaluacionDirectorController extends Controller
{
    public function store(Request $request, Proyecto $proyecto)
    {
        abort_unless(
            (int) $proyecto->profesor_id === (int) $request->user()->id,
            403
        );

        $datos = $request->validate([
            'nota' => ['required', 'numeric', 'between:0,5'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
            'nueva_fecha_limite' => [
                'nullable',
                'required_if:nota,0,0.0,0.00',
                'date',
                'after:today',
            ],
        ]);

        DB::transaction(function () use ($proyecto, $datos, $request) { 

            $proyecto = Proyecto::whereKey($proyecto->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($proyecto->estado !== 'activo') {
                throw ValidationException::withMessages([
                    'nota' => 'El proyecto no está activo.',
                ]);
            }

            if ($proyecto->fase_evaluacion !== 'pendiente_evaluacion_1') {
                throw ValidationException::withMessages([
                    'nota' => 'Este proyecto no está pendiente de evaluación 1.',
                ]);
            }

            $nota = (float) $datos['nota'];

            if ($nota < 3.0 && empty($datos['nueva_fecha_limite'])) {
                throw ValidationException::withMessages([
                    'nueva_fecha_limite' =>
                        'Debes establecer un nuevo plazo para las correcciones.',
                ]);
            }

            $numeroIntento =
                $proyecto->evaluacionesDirector()->count() + 1;

            $proyecto->evaluacionesDirector()->create([
                'profesor_id' => $request->user()->id,
                'nota' => $nota,
                'observaciones' => $datos['observaciones'] ?? null,
                'nueva_fecha_limite' =>
                    $nota < 3.0
                        ? $datos['nueva_fecha_limite']
                        : null,
                'numero_intento' => $numeroIntento,
            ]);

            if ($nota >= 3.0) {
                $proyecto->update([
                    'fase_evaluacion' => 'pendiente_jurados',
                    'fecha_limite_correccion' => null,
                ]);
            } else {
                $proyecto->update([
                    'fase_evaluacion' => 'correcciones',
                    'fecha_limite_correccion' =>
                        $datos['nueva_fecha_limite'],
                ]);
            }
        });

        if ((float) $datos['nota'] >= 3.0) {
            app(AsignacionJuradosService::class)
                ->completarInvitaciones($proyecto);
        } //al aprobar la evaluacion 1, se asignan los jurados automaticamente

        return redirect()
            ->route('profesor.proyectos.show', $proyecto)
            ->with('success', 'Evaluación registrada correctamente.');
    }
}