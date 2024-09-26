<?php

namespace App\Jobs;

use App\Models\AcuseDeRecibo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogApiTrafficJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Crear una nueva instancia del trabajo.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Ejecutar el trabajo.
     */
    public function handle()
    {
        AcuseDeRecibo::create($this->data);
    }
}
