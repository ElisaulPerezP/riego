<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Events\ApiTrafficLogged;
use Illuminate\Support\Facades\Auth;

class LogApiTraffic
{
    /**
     * Manejar una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Continuar con la solicitud y obtener la respuesta
        $response = $next($request);

        // Preparar los datos para el registro
        $data = [
            'entregado_a'        => $request->header('User-Agent'), // O el identificador del receptor
            'acuse_de_recibo'    => $response->getContent(),
            'recibido_de'        => $request->ip(),
            'modelo_serializado' => [
                'method' => $request->method(),
                'input' => $request->all(),
            ],
            'fecha_entrega'      => now(),
            'fecha_acuse'        => now(),
            'estado_entrega'     => $response->getStatusCode(),
            'usuario_responsable'=> Auth::check() ? Auth::id() : null,
            'firma_recibo'       => hash('sha256', $request->getContent()),
        ];

        // Despachar el evento con los datos serializables
        event(new ApiTrafficLogged($data));

        return $response;
    }
}
