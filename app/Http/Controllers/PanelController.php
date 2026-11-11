<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PanelController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        return match ($usuario->role) {
            'administrador' => view('panel.administrador'),
            'profesor' => view('panel.profesor'),
            'estudiante' => view('panel.estudiante'),
            default => abort(403, 'Rol no autorizado'),
        };
    }
}