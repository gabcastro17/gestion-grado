<?php

namespace App\Services;

use App\Models\Jurado;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AsignacionJuradosService
{
    public function completarInvitaciones(Proyecto $proyecto): void
    {
        DB::transaction(function () use ($proyecto) {
            $proyecto = Proyecto::query()
                ->lockForUpdate()
                ->findOrFail($proyecto->id);

            if (
                $proyecto->estado !== 'activo' ||
                $proyecto->fase_evaluacion !== 'pendiente_jurados'
            ) {
                return;
            }

            $ocupados = Jurado::query()
                ->where('proyecto_id', $proyecto->id)
                ->whereIn('estado', ['pendiente', 'aceptado'])
                ->count();

            $faltantes = max(0, 2 - $ocupados);

            if ($faltantes === 0) {
                return;
            }

            // Excluir al director y a cualquier profesor
            // que ya haya recibido una invitación.
            $yaInvitados = Jurado::query()
                ->where('proyecto_id', $proyecto->id)
                ->pluck('profesor_id');

            $candidatos = User::query()
                ->where('role', 'profesor')
                ->where('activo', true)
                ->where('id', '!=', $proyecto->profesor_id)
                ->whereNotIn('id', $yaInvitados)
                ->inRandomOrder()
                ->limit($faltantes)
                ->get();

            foreach ($candidatos as $profesor) {
                Jurado::create([
                    'proyecto_id' => $proyecto->id,
                    'profesor_id' => $profesor->id,
                    'estado' => 'pendiente',
                    'fecha_asignacion' => now(),
                ]);
            }
        });
    }
}

//selecciona profesores activos
//excluye al director del proyecto 
// evita seleccionar dos veces al mismo profesor
// no vuelve a invitar a alguien que ya rechazó
// mantiene como maximo 2 invitaciones activas entre pendientes y aceptadas 
//si no hay suficientes profes disponibles, deja los cupos restantes sin cubrir hasta que existan candidatos
