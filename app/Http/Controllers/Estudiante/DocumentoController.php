<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\Entrega;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DocumentoController extends Controller
{
    public function show(
        Request $request,
        Proyecto $proyecto,
        Entrega $entrega
    ) {
        $this->autorizar($request, $proyecto, $entrega);

        $entrega->load([
            'documentos',
            'retroalimentaciones.profesor',
        ]);

        return view('estudiante.entregas.show', compact(
            'proyecto',
            'entrega'
        ));
    }

    public function store(
        Request $request,
        Proyecto $proyecto,
        Entrega $entrega
    ) {
        $this->autorizar($request, $proyecto, $entrega);

        if ($proyecto->estado !== 'activo') {
            throw ValidationException::withMessages([
                'entrega' => 'El proyecto no está activo.',
            ]);
        }

        // Bloquear documentos después de aprobar evaluación 1.
        if (in_array($proyecto->fase_evaluacion, [
            'pendiente_jurados',
            'evaluacion_2',
            'finalizado',
        ], true)) {
            throw ValidationException::withMessages([
                'entrega' => 'Este proyecto ya pasó a jurados y no admite nuevos documentos.',
            ]);
        }

        // Comprobar las fechas según la fase.
        if ($proyecto->fase_evaluacion === 'correcciones') {
            if (
                !$proyecto->fecha_limite_correccion ||
                now()->startOfDay()->gt(
                    $proyecto->fecha_limite_correccion
                )
            ) {
                throw ValidationException::withMessages([
                    'entrega' => 'El plazo para presentar correcciones ha vencido.',
                ]);
            }
        } else {
            if (
                $entrega->fecha_inicio &&
                now()->lt($entrega->fecha_inicio->copy()->startOfDay())
            ) {
                throw ValidationException::withMessages([
                    'entrega' => 'La entrega todavía no está habilitada.',
                ]);
            }

            if (
                $entrega->fecha_limite &&
                now()->gt($entrega->fecha_limite->copy()->endOfDay())
            ) {
                throw ValidationException::withMessages([
                    'entrega' => 'La fecha límite de esta entrega ya pasó.',
                ]);
            }
        }

        $datos = $request->validate([
            'archivo' => [
                'nullable',
                'required_without:enlace',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240',
            ],
            'enlace' => [
                'nullable',
                'required_without:archivo',
                'url:http,https',
                'max:2000',
            ],
        ]);

        if ($request->hasFile('archivo') && $request->filled('enlace')) {
            throw ValidationException::withMessages([
                'archivo' => 'Envía un archivo o un enlace, no ambos.',
            ]);
        }

        $ruta = null;

        try {
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo');

                // Guardar el documento en Supabase Storage.
                $ruta = $archivo->store(
                    'documentos/proyecto-' . $proyecto->id,
                    's3'
                );

                if (!$ruta) {
                    throw ValidationException::withMessages([
                        'archivo' => 'No se pudo subir el documento.',
                    ]);
                }

                $documento = [
                    'nombre_archivo' => $archivo->getClientOriginalName(),
                    'ruta_archivo' => $ruta,
                    'enlace' => null,
                    'tipo_archivo' => $archivo->extension(),
                ];
            } else {
                $documento = [
                    'nombre_archivo' => 'Enlace de entrega',
                    'ruta_archivo' => null,
                    'enlace' => $datos['enlace'],
                    'tipo_archivo' => 'enlace',
                ];
            }

            DB::transaction(function () use (
                $request,
                $proyecto,
                $entrega,
                $documento
            ) {
                Documento::create([
                    'entrega_id' => $entrega->id,
                    'estudiante_id' => $request->user()->id,
                    ...$documento,
                ]);

                $entrega->update([
                    'estado' => 'entregada',
                ]);

                // Después de subir la corrección,
                // el director puede evaluar nuevamente.
                if ($proyecto->fase_evaluacion === 'correcciones') {
                    $proyecto->update([
                        'fase_evaluacion' => 'pendiente_evaluacion_1',
                        'fecha_limite_correccion' => null,
                    ]);
                }
            });
        } catch (\Throwable $error) {
            // Evitar dejar archivos huérfanos si falla la BD.
            if ($ruta !== null) {
                try {
                    Storage::disk('s3')->delete($ruta);
                } catch (\Throwable $deleteError) {
                    report($deleteError);
                }
            }

            throw $error;
        }

        return redirect()
            ->route('estudiante.entregas.show', [
                $proyecto,
                $entrega,
            ])
            ->with(
                'success',
                'Documento enviado correctamente.'
            );
    }

    private function autorizar(
        Request $request,
        Proyecto $proyecto,
        Entrega $entrega
    ): void {
        abort_unless(
            (int) $proyecto->estudiante_id === (int) $request->user()->id,
            403
        );

        abort_unless(
            (int) $entrega->proyecto_id === (int) $proyecto->id,
            404
        );
    }
}
