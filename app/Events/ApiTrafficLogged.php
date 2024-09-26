<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApiTrafficLogged
{
    use Dispatchable, SerializesModels;

    public $data;

    /**
     * Crear una nueva instancia del evento.
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
