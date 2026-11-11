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
    Storage::disk('s3')->exists($documento->ruta_archivo),
        404,
        'El documento no existe en Supabase Storage.'
    );

    $stream = Storage::disk('s3')->readStream(
    $documento->ruta_archivo
    );

    if ($stream === false) {
        abort(404, 'No se pudo encontrar el documento.');
    }

    return response()->streamDownload(
        function () use ($stream) {
            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
    },
    $documento->nombre_archivo,
    [
        'Content-Type' => 'application/octet-stream',
    ]
);
    }
}