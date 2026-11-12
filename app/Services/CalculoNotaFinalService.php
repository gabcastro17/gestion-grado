<?php

namespace App\Services;

use App\Models\EvaluacionDirector;
use App\Models\Jurado;
use App\Models\Proyecto;

class CalculoNotaFinalService
{
    public function calcular(Proyecto $proyecto): ?array
    {
        //ultima evaluación del director
        $evaluacion = EvaluacionDirector::query()
            ->where('proyecto_id', $proyecto->id)
            ->orderByDesc('numero_intento')
            ->first();

        if (
            !$evaluacion ||
            (float) $evaluacion->nota < 3.0
        ) {
            return null;
        }

        //solo jurados que aceptaron
        $jurados = Jurado::query()
            ->with('calificacion')
            ->where('proyecto_id', $proyecto->id)
            ->where('estado', 'aceptado')
            ->get();

        //deben existir exactamente dos jurados aceptados.
        if ($jurados->count() !== 2) {
            return null;
        }

        //ambos deben haber registrado su nota
        if ($jurados->contains(
            fn ($jurado) => $jurado->calificacion === null
        )) {
            return null;
        }

        $nota1 = (float) $evaluacion->nota;

        $nota2 = $jurados->avg(
            fn ($jurado) => (float) $jurado->calificacion->nota
        );

        $notaFinal = ($nota1 + $nota2) / 2;

        return [
            'evaluacion_1' => round($nota1, 2),
            'evaluacion_2' => round($nota2, 2),
            'nota_final' => round($notaFinal, 2),
            'aprobado' => $notaFinal >= 3.0,
        ];
    }
}
