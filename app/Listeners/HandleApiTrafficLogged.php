<?php

namespace App\Listeners;

use App\Events\ApiTrafficLogged;
use App\Models\AcuseDeRecibo;

class HandleApiTrafficLogged
{
    /**
     * Manejar el evento.
     *
     * @param  \App\Events\ApiTrafficLogged  $event
     * @return void
     */
    public function handle(ApiTrafficLogged $event)
    {
        $data = $event->data;

        AcuseDeRecibo::create([
            'entregado_a'        => $data['entregado_a'],
            'acuse_de_recibo'    => $data['acuse_de_recibo'],
            'recibido_de'        => $data['recibido_de'],
            'modelo_serializado' => $data['modelo_serializado'],
            'fecha_entrega'      => $data['fecha_entrega'],
            'fecha_acuse'        => $data['fecha_acuse'],
            'estado_entrega'     => $data['estado_entrega'],
            'usuario_responsable'=> $data['usuario_responsable'],
            'firma_recibo'       => $data['firma_recibo'],
        ]);
    }
}
