<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoDescargaController extends Controller
{
    public function descargar(Request $request, Documento $documento)
    {
        $documento->load('entrega.proyecto');

        $proyecto = $documento->entrega->proyecto;
        $usuario = $request->user();

        abort_unless($usuario->activo, 403);

        $esEstudiante = $usuario->role === 'estudiante'
            && (int) $proyecto->estudiante_id === (int) $usuario->id;

        $esProfesor = $usuario->role === 'profesor'
            && (int) $proyecto->profesor_id === (int) $usuario->id;

        abort_unless($esEstudiante || $esProfesor, 403);

        abort_unless($documento->ruta_archivo, 404);

        abort_unless(
            Storage::disk('local')->exists($documento->ruta_archivo),
            404
        );

        return response()->download(
            Storage::disk('local')->path($documento->ruta_archivo),
            $documento->nombre_archivo
        );
    }
}