<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\Propuesta;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        $solicitudes = Solicitud::with('propuesta.profesor')
            ->where('estudiante_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('estudiante.solicitudes.index', compact('solicitudes'));
    }

    public function store(Request $request, Propuesta $propuesta)
    {
        $datos = $request->validate([
            'mensaje' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $propuesta, $datos) {
            // bloquear el usuario para evitar solicitudes al mismo tiempo
            $estudiante = \App\Models\User::whereKey($request->user()->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!$estudiante->activo || $estudiante->role !== 'estudiante') {
                abort(403);
            }

            $propuestaActual = Propuesta::whereKey($propuesta->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($propuestaActual->estado !== 'publicada') {
                throw ValidationException::withMessages([
                    'solicitud' => 'Esta propuesta ya no está disponible.',
                ]);
            }

            $tieneProyecto = Proyecto::where(
                'estudiante_id',
                $estudiante->id
            )->where('estado', 'activo')->exists();

            if ($tieneProyecto) {
                throw ValidationException::withMessages([
                    'solicitud' => 'Ya tienes un proyecto activo.',
                ]);
            }

            $tieneSolicitudPendiente = Solicitud::where(
                'estudiante_id',
                $estudiante->id
            )->where('estado', 'pendiente')->exists();

            if ($tieneSolicitudPendiente) {
                throw ValidationException::withMessages([
                    'solicitud' => 'Ya tienes una solicitud pendiente.',
                ]);
            }

            Solicitud::create([
                'estudiante_id' => $estudiante->id,
                'propuesta_id' => $propuestaActual->id,
                'estado' => 'pendiente',
                'mensaje' => $datos['mensaje'] ?? null,
                'fecha_solicitud' => now(),
            ]);
        });

        return redirect()
            ->route('estudiante.solicitudes.index')
            ->with('success', 'Solicitud enviada correctamente.');
    }
}