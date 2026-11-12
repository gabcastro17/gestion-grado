
@extends('layouts.app')

@section('content')

<div class="container py-4">

    <a href="{{ route('panel') }}"
       class="btn btn-outline-secondary mb-4">
        Volver al panel
    </a>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <h2 class="fw-bold mb-3">
                Vincular Telegram
            </h2>

            <p>
                Conecta tu cuenta para consultar tu
                proyecto, entregas y calificaciones
                desde Telegram.
            </p>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($vinculado)

                <div class="alert alert-success">
                    Tu cuenta ya está vinculada
                    con Telegram.
                </div>

            @else

                <form method="POST"
                      action="{{ route('telegram.generar-codigo') }}">
                    @csrf

                    <button type="submit"
                            class="btn btn-primary">
                        Generar código de vinculación
                    </button>
                </form>

                @if(session('codigo_telegram'))

                    <div class="alert alert-info mt-4">
                        <p class="mb-2">
                            Envía este código al bot
                            usando el comando /start:
                        </p>

                        <h3 class="fw-bold">
                            {{ session('codigo_telegram') }}
                        </h3>

                        <p class="mb-0">
                            En Telegram escribe:
                            <strong>
                                /start {{ session('codigo_telegram') }}
                            </strong>
                        </p>

                        <small>
                            El código vence en 10 minutos.
                            No lo compartas con otras personas.
                        </small>
                    </div>

                @endif

            @endif

        </div>
    </div>

</div>

@endsection
