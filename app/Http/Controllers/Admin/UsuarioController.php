<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    // Mostrar todos los usuarios
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $usuarios = User::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('name', 'like', "%{$buscar}%")
                      ->orWhere('email', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.usuarios.index', compact('usuarios', 'buscar'));
    }

    // Mostrar formulario de registro
    public function create()
    {
        return view('admin.usuarios.create');
    }

    // Guardar un usuario nuevo
    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['estudiante', 'profesor'])],
        ]);

        User::create([
            'name' => $datos['name'],
            'email' => Str::lower(trim($datos['email'])),
            'password' => Str::random(64),
            'role' => $datos['role'],
            'activo' => true,
        ]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit(User $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    // Actualizar un usuario
    public function update(Request $request, User $usuario)
    {
        if ($usuario->role === 'administrador') {
            abort(403, 'No puedes modificar una cuenta administradora desde este módulo.');
        }

        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'role' => ['required', Rule::in(['estudiante', 'profesor'])],
        ]);

        $usuario->update([
            'name' => $datos['name'],
            'email' => Str::lower(trim($datos['email'])),
            'role' => $datos['role'],
        ]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    // Activar o desactivar un usuario
    public function cambiarEstado(User $usuario)
    {
        if ($usuario->role === 'administrador') {
            abort(403, 'No puedes desactivar una cuenta administradora.');
        }

        $usuario->update([
            'activo' => !$usuario->activo,
        ]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Estado del usuario actualizado.');
    }
}