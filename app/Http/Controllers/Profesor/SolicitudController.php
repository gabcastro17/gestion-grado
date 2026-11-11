<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\Propuesta;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        $solicitudes = Solicitud::with([
            'estudiante',
            'propuesta',
        ])
        ->whereHas('propuesta', function ($query) use ($request) {
            $query->where('profesor_id', $request->user()->id);
        })
        ->latest()
        ->paginate(10);

        return view('profesor.solicitudes.index', compact('solicitudes'));
    }

    public function aceptar(Request $request, Solicitud $solicitud)
    {
        DB::transaction(function () use ($request, $solicitud) {
            // Bloquear primero al estudiante para mantener
            // el mismo orden de bloqueo en ambos controladores.
            $estudiante = User::whereKey($solicitud->estudiante_id)
                ->lockForUpdate()
                ->firstOrFail();

            $solicitudActual = Solicitud::whereKey($solicitud->id)
                ->lockForUpdate()
                ->firstOrFail();

            $propuesta = Propuesta::whereKey(
                $solicitudActual->propuesta_id
            )->lockForUpdate()->firstOrFail();

            abort_unless(
                $propuesta->profesor_id === $request->user()->id,
                403
            );

            if ($solicitudActual->estado !== 'pendiente') {
                throw ValidationException::withMessages([
                    'solicitud' => 'Esta solicitud ya fue respondida.',
                ]);
            }

            if ($propuesta->estado !== 'publicada') {
                throw ValidationException::withMessages([
                    'solicitud' => 'La propuesta ya no está publicada.',
                ]);
            }

            if (!$estudiante->activo || $estudiante->role !== 'estudiante') {
                throw ValidationException::withMessages([
                    'solicitud' => 'El estudiante no tiene una cuenta válida.',
                ]);
            }

            $tieneProyecto = Proyecto::where(
                'estudiante_id',
                $estudiante->id
            )->where('estado', 'activo')->exists();

            if ($tieneProyecto) {
                throw ValidationException::withMessages([
                    'solicitud' => 'El estudiante ya tiene un proyecto activo.',
                ]);
            }

            $solicitudActual->update([
                'estado' => 'aceptada',
                'fecha_respuesta' => now(),
            ]);

            Proyecto::create([
                'solicitud_id' => $solicitudActual->id,
                'estudiante_id' => $estudiante->id,
                'profesor_id' => $propuesta->profesor_id,
                'propuesta_id' => $propuesta->id,
                'estado' => 'activo',
                'fecha_inicio' => now()->toDateString(),
            ]);

            // La propuesta deja de aceptar solicitudes.
            $propuesta->update([
                'estado' => 'cerrada',
            ]);

            // Rechazar otras solicitudes pendientes
            // que existieran para esta propuesta.
            Solicitud::where('propuesta_id', $propuesta->id)
                ->where('estado', 'pendiente')
                ->update([
                    'estado' => 'rechazada',
                    'fecha_respuesta' => now(),
                ]);
        });

        return redirect()
            ->route('profesor.solicitudes.index')
            ->with('success', 'Solicitud aceptada. El proyecto fue creado.');
    }

    public function rechazar(Request $request, Solicitud $solicitud)
    {
        DB::transaction(function () use ($request, $solicitud) {
            $solicitudActual = Solicitud::whereKey($solicitud->id)
                ->lockForUpdate()
                ->firstOrFail();

            $propuesta = Propuesta::findOrFail(
                $solicitudActual->propuesta_id
            );

            abort_unless(
                $propuesta->profesor_id === $request->user()->id,
                403
            );

            if ($solicitudActual->estado !== 'pendiente') {
                throw ValidationException::withMessages([
                    'solicitud' => 'Esta solicitud ya fue respondida.',
                ]);
            }

            $solicitudActual->update([
                'estado' => 'rechazada',
                'fecha_respuesta' => now(),
            ]);
        });

        return redirect()
            ->route('profesor.solicitudes.index')
            ->with('success', 'Solicitud rechazada.');
    }
}