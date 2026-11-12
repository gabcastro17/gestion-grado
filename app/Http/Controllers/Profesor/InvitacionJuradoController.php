<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Jurado;
use App\Services\AsignacionJuradosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvitacionJuradoController extends Controller
{
    public function index(Request $request)
    {
        $invitaciones = Jurado::query()
            ->with('proyecto')
            ->where('profesor_id', $request->user()->id)
            ->latest('id')
            ->get();

        return view(
            'profesor.jurados.index',
            compact('invitaciones')
        );
    }

    public function aceptar(Request $request, Jurado $jurado)
    {
        abort_unless(
            (int) $jurado->profesor_id ===
            (int) $request->user()->id,
            403
        );

        DB::transaction(function () use ($jurado) {
            $invitacion = Jurado::query()
                ->lockForUpdate()
                ->findOrFail($jurado->id);

            if ($invitacion->estado !== 'pendiente') {
                abort(422, 'Esta invitación ya fue respondida.');
            }

            $invitacion->update([
                'estado' => 'aceptado',
                'fecha_respuesta' => now(),
            ]);
        });

        return redirect()
            ->route('profesor.jurados.index')
            ->with('success', 'Invitación aceptada.');
    }

    public function rechazar(
        Request $request,
        Jurado $jurado,
        AsignacionJuradosService $asignador
    ) {
        abort_unless(
            (int) $jurado->profesor_id ===
            (int) $request->user()->id,
            403
        );

        $proyecto = DB::transaction(function () use ($jurado) {
            $invitacion = Jurado::query()
                ->with('proyecto')
                ->lockForUpdate()
                ->findOrFail($jurado->id);

            if ($invitacion->estado !== 'pendiente') {
                abort(422, 'Esta invitación ya fue respondida.');
            }

            $invitacion->update([
                'estado' => 'rechazado',
                'fecha_respuesta' => now(),
            ]);

            return $invitacion->proyecto;
        });

        // Buscar un profesor sustituto.
        $asignador->completarInvitaciones($proyecto);

        return redirect()
            ->route('profesor.jurados.index')
            ->with(
                'success',
                'Invitación rechazada. Se buscará otro jurado.'
            );
    }
}
