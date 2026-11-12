<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use App\Models\Jurado;
use App\Models\Proyecto;
use App\Services\CalculoNotaFinalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CalificacionJuradoController extends Controller
{
    public function store(Request $request, Jurado $jurado)
    {
        $datos = $request->validate([
            'nota' => ['required', 'numeric', 'between:0,5'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($request, $jurado, $datos) {

            // Bloquear el proyecto para evitar cierres simultáneos.
            $proyecto = Proyecto::query()
                ->lockForUpdate()
                ->findOrFail($jurado->proyecto_id);

            // Consultar la invitación del profesor.
            $jurado = Jurado::query()
                ->lockForUpdate()
                ->findOrFail($jurado->id);

            // Solo el profesor invitado puede calificar.
            abort_unless(
                (int) $jurado->profesor_id ===
                (int) $request->user()->id,
                403
            );

            // Debe haber aceptado su invitación.
            if ($jurado->estado !== 'aceptado') {
                throw ValidationException::withMessages([
                    'nota' => 'Debes aceptar la invitación antes de calificar.',
                ]);
            }

            // El proyecto debe estar activo y en evaluación 2.
            if (
                $proyecto->estado !== 'activo' ||
                !in_array(
                    $proyecto->fase_evaluacion,
                    ['pendiente_jurados', 'evaluacion_2'],
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'nota' => 'Este proyecto no está habilitado para evaluación 2.',
                ]);
            }

            // Deben existir exactamente dos jurados aceptados.
            $aceptados = Jurado::query()
                ->where('proyecto_id', $proyecto->id)
                ->where('estado', 'aceptado')
                ->count();

            if ($aceptados !== 2) {
                throw ValidationException::withMessages([
                    'nota' => 'Deben existir exactamente dos jurados aceptados.',
                ]);
            }

            // Cada jurado solo puede registrar una nota.
            if (
                Calificacion::query()
                    ->where('jurado_id', $jurado->id)
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'nota' => 'Ya registraste tu calificación.',
                ]);
            }

            // Guardar la nota del jurado.
            Calificacion::create([
                'jurado_id' => $jurado->id,
                'nota' => $datos['nota'],
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            // Calcular el resultado con las notas ya guardadas.
            $resultado = app(
                CalculoNotaFinalService::class
            )->calcular($proyecto);

            if ($resultado !== null) {

                // Ambas evaluaciones están completas.
                $proyecto->update([
                    'fase_evaluacion' => 'finalizado',
                    'estado' => 'finalizado',
                ]);

            } else {

                // Todavía falta la nota de algún jurado.
                $proyecto->update([
                    'fase_evaluacion' => 'evaluacion_2',
                ]);
            }
        });

        return redirect()
            ->route('profesor.jurados.index')
            ->with(
                'success',
                'Calificación registrada correctamente.'
            );
    }
}
