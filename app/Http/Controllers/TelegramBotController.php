<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\TelegramLinkCode;
use App\Models\User;
use App\Services\CalculoNotaFinalService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    public function webhook(
        Request $request,
        TelegramService $telegram,
        CalculoNotaFinalService $calculadora
    ) {
        
        $secreto = config('services.telegram.webhook_secret');

        if (
            !$secreto ||
            !hash_equals(
                $secreto,
                (string) $request->header(
                    'X-Telegram-Bot-Api-Secret-Token'
                )
            )
        ) {
            abort(403);
        }

        $mensaje = $request->input('message');

        if (!is_array($mensaje) || !isset($mensaje['text'])) {
            return response()->json(['ok' => true]);
        }

        // No atender mensajes de grupos.
        if (($mensaje['chat']['type'] ?? '') !== 'private') {
            return response()->json(['ok' => true]);
        }

        $chatId = (string) ($mensaje['chat']['id'] ?? '');
        $texto = trim((string) $mensaje['text']);

        if ($chatId === '') {
            return response()->json(['ok' => true]);
        }

        $comando = strtolower(
            strtok($texto, " \n\r\t") ?: ''
        );

        // Permitir comandos como /proyecto@NombreDelBot.
        $comando = preg_replace('/@\w+$/', '', $comando);

        if (
            $comando === '/start' &&
            preg_match('/^\/start(?:@\w+)?\s+(\d{8})$/i', $texto, $partes)
        ) {
            $respuesta = $this->vincular(
                $chatId,
                $partes[1]
            );
        } elseif ($comando === '/start') {
            $respuesta = "¡Bienvenido a Gestión de Grado!\n\n"
                . "Para vincular tu cuenta:\n"
                . "1. Inicia sesión en la plataforma.\n"
                . "2. Abre /telegram/vincular.\n"
                . "3. Genera un código temporal.\n"
                . "4. Envíame /start seguido del código.\n\n"
                . "Comandos disponibles:\n"
                . "/proyecto\n"
                . "/entregas\n"
                . "/nota";
        } else {
            $usuario = User::query()
                ->where('telegram_chat_id', $chatId)
                ->where('activo', true)
                ->first();

            if (!$usuario) {
                $respuesta = "Primero debes vincular tu cuenta.\n\n"
                    . "Inicia sesión en Gestión de Grado, "
                    . "genera tu código de Telegram y envía:\n"
                    . "/start TU_CODIGO";
            } elseif ($usuario->role !== 'estudiante') {
                $respuesta = 'Estos comandos de consulta '
                    . 'están disponibles para estudiantes.';
            } else {
                $respuesta = match ($comando) {
                    '/proyecto' => $this->consultarProyecto($usuario),
                    '/entregas' => $this->consultarEntregas($usuario),
                    '/nota' => $this->consultarNota(
                        $usuario,
                        $calculadora
                    ),
                    default => "Comando no reconocido.\n\n"
                        . "Usa /proyecto, /entregas o /nota.",
                };
            }
        }

        try {
            $telegram->enviarMensaje($chatId, $respuesta);
        } catch (\Throwable $e) {
            Log::error('Error enviando respuesta a Telegram', [
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(['ok' => true]);
    }

    private function proyectoDelEstudiante(User $usuario): ?Proyecto
    {
        return Proyecto::query()
            ->with(['propuesta', 'profesor'])
            ->where('estudiante_id', $usuario->id)
            ->latest('id')
            ->first();
    }

    private function consultarProyecto(User $usuario): string
    {
        $proyecto = $this->proyectoDelEstudiante($usuario);

        if (!$proyecto) {
            return 'Todavía no tienes un proyecto de grado asignado.';
        }

        $titulo = $proyecto->propuesta?->titulo
            ?? 'Proyecto de grado';

        $director = $proyecto->profesor?->name
            ?? 'Sin asignar';

        return "📚 TU PROYECTO DE GRADO\n\n"
            . "Título: {$titulo}\n"
            . "Director: {$director}\n"
            . "Estado: {$proyecto->estado}\n"
            . "Fase: {$proyecto->fase_evaluacion}";
    }

    private function consultarEntregas(User $usuario): string
    {
        $proyecto = $this->proyectoDelEstudiante($usuario);

        if (!$proyecto) {
            return 'Todavía no tienes un proyecto de grado asignado.';
        }

        $entregas = $proyecto->entregas()
            ->orderBy('numero_orden')
            ->get();

        if ($entregas->isEmpty()) {
            return 'Tu proyecto todavía no tiene entregas programadas.';
        }

        $respuesta = "📅 ENTREGAS DE TU PROYECTO\n\n";

        foreach ($entregas as $entrega) {
            $fecha = $entrega->fecha_limite
                ? \Illuminate\Support\Carbon::parse(
                    $entrega->fecha_limite
                )->format('d/m/Y')
                : 'Sin definir';

            $respuesta .= "• {$entrega->titulo}\n"
                . "Fecha límite: {$fecha}\n"
                . "Estado: {$entrega->estado}\n\n";
        }

        return mb_substr($respuesta, 0, 4000);
    }

    private function consultarNota(
        User $usuario,
        CalculoNotaFinalService $calculadora
    ): string {
        $proyecto = $this->proyectoDelEstudiante($usuario);

        if (!$proyecto) {
            return 'Todavía no tienes un proyecto de grado asignado.';
        }

        $resultado = $calculadora->calcular($proyecto);

        if (!$resultado) {
            return "📝 CALIFICACIONES\n\n"
                . "Tu proyecto todavía no tiene "
                . "las dos evaluaciones completas.\n"
                . "Fase actual: {$proyecto->fase_evaluacion}";
        }

        $nota1 = number_format(
            $resultado['evaluacion_1'],
            2
        );

        $nota2 = number_format(
            $resultado['evaluacion_2'],
            2
        );

        $final = number_format(
            $resultado['nota_final'],
            2
        );

        $estado = $resultado['aprobado']
            ? 'APROBADO'
            : 'NO APROBADO';

        return "🎓 RESULTADO FINAL\n\n"
            . "Evaluación 1: {$nota1}\n"
            . "Evaluación 2: {$nota2}\n"
            . "Nota final: {$final}\n\n"
            . "Resultado: {$estado}";
    }

    private function vincular(string $chatId, string $codigo): string
    {
        return DB::transaction(function () use ($chatId, $codigo) {
            $yaVinculado = User::query()
                ->where('telegram_chat_id', $chatId)
                ->first();

            if ($yaVinculado) {
                return 'Este chat ya está vinculado '
                    . 'con una cuenta de Gestión de Grado.';
            }

            $registros = TelegramLinkCode::query()
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->get();

            foreach ($registros as $registro) {
                if (!Hash::check($codigo, $registro->code_hash)) {
                    continue;
                }

                $usuario = User::query()
                    ->lockForUpdate()
                    ->find($registro->user_id);

                if (!$usuario || !$usuario->activo) {
                    return 'La cuenta no está disponible.';
                }

                if ($usuario->telegram_chat_id) {
                    return 'Esta cuenta ya está vinculada '
                        . 'con otro chat de Telegram.';
                }

                $usuario->telegram_chat_id = $chatId;
                $usuario->save();

                $registro->delete();

                return "¡Cuenta vinculada correctamente!\n\n"
                    . "Hola, {$usuario->name}.\n\n"
                    . "Ya puedes usar:\n"
                    . "/proyecto\n"
                    . "/entregas\n"
                    . "/nota";
            }

            return 'El código no es válido o ha vencido. '
                . 'Genera uno nuevo desde la plataforma.';
        });
    }
}
