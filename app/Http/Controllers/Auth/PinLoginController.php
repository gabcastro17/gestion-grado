<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CodigoPin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PinLoginController extends Controller
{
    public function mostrarLogin()
    {
        return view('auth.login');
    }

    public function enviarPin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = Str::lower(trim($request->email));

        // Limitar solicitudes por correo y dirección IP.
        $limite = 'solicitar-pin:' . sha1($email . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($limite, 3)) {
            throw ValidationException::withMessages([
                'email' => 'Demasiadas solicitudes. Intenta más tarde.',
            ]);
        }

        RateLimiter::hit($limite, 300);

        $usuario = User::where('email', $email)
            ->where('activo', true)
            ->first();

        // Respuesta genérica para no revelar qué correos existen.
        $mensaje = 'Si el correo está registrado y activo, recibirás un PIN.';

        if (!$usuario) {
            return back()->with('success', $mensaje);
        }

        // Generar un PIN de seis dígitos.
        $pin = (string) random_int(100000, 999999);

        // Invalidar los PIN anteriores.
        CodigoPin::where('user_id', $usuario->id)
            ->where('usado', false)
            ->update(['usado' => true]);

        // Guardar solamente el hash.
        CodigoPin::create([
            'user_id' => $usuario->id,
            'codigo' => Hash::make($pin),
            'expiracion' => now()->addMinutes(5),
            'usado' => false,
        ]);

        // Enviar el correo.
        Mail::raw(
            "Tu código de acceso a Gestión de Grado es: {$pin}\n\n".
            "Este código vence en cinco minutos y solo puede utilizarse una vez.",
            function ($message) use ($usuario) {
                $message->to($usuario->email)
                    ->subject('Código de acceso - Gestión de Grado');
            }
        );

        return redirect()
            ->route('pin.formulario', ['email' => $email])
            ->with('success', $mensaje);
    }

    public function mostrarFormularioPin(Request $request)
    {
        return view('auth.verificar-pin', [
            'email' => $request->query('email', ''),
        ]);
    }

    public function verificarPin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'pin' => ['required', 'digits:6'],
        ]);

        $email = Str::lower(trim($request->email));

        $limite = 'verificar-pin:' . sha1($email . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($limite, 5)) {
            throw ValidationException::withMessages([
                'pin' => 'Demasiados intentos. Solicita un nuevo PIN más tarde.',
            ]);
        }

        $usuario = User::where('email', $email)
            ->where('activo', true)
            ->first();

        $codigo = $usuario
            ? CodigoPin::where('user_id', $usuario->id)
                ->where('usado', false)
                ->where('expiracion', '>', now())
                ->latest('id')
                ->first()
            : null;

        if (!$codigo || !Hash::check($request->pin, $codigo->codigo)) {
            RateLimiter::hit($limite, 300);

            throw ValidationException::withMessages([
                'pin' => 'El código es incorrecto o ha vencido.',
            ]);
        }

        // Evitar reutilizar el código.
        $codigo->update(['usado' => true]);

        RateLimiter::clear($limite);

        // Iniciar sesión.
        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('panel');
    }

    public function cerrarSesion(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}