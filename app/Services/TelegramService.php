<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    public function enviarMensaje(
        int|string $chatId,
        string $mensaje
    ): void {
        $token = config('services.telegram.token');

        if (!$token) {
            throw new \RuntimeException(
                'No se configuró TELEGRAM_BOT_TOKEN.'
            );
        }

        Http::timeout(10)
            ->post(
                "https://api.telegram.org/bot{$token}/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => $mensaje,
                ]
            )
            ->throw();
    }
}
