<?php

namespace App\Http\Controllers;

use App\Models\TelegramLinkCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TelegramVinculacionController extends Controller
{
    public function mostrar(Request $request)
    {
        return view('telegram.vincular', [
            'vinculado' => filled(
                $request->user()->telegram_chat_id
            ),
        ]);
    }

    public function generar(Request $request)
    {
        $usuario = $request->user();

        if (filled($usuario->telegram_chat_id)) {
            return back()->with(
                'success',
                'Tu cuenta ya está vinculada con Telegram.'
            );
        }

        $codigo = (string) random_int(10000000, 99999999);

        TelegramLinkCode::updateOrCreate(
            ['user_id' => $usuario->id],
            [
                'code_hash' => Hash::make($codigo),
                'expires_at' => now()->addMinutes(10),
            ]
        );

        return back()->with(
            'codigo_telegram',
            $codigo
        );
    }
}
