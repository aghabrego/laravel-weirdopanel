<?php

namespace WeirdoPanel\Commands\Actions;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Reinstall extends Command
{

    protected $signature = 'panel:reinstall';
    protected $description = 'Reinstalar todo el paquete';

    public function handle()
    {
        $status = $this->confirm("¿Realmente desea reinstalar el panel? ? (Todos los componentes serán eliminados.)", true);

        if(!$status) {
            $this->info("El proceso fue cancelado");
            return;
        }

        Artisan::call("panel:uninstall", [
            '--force' => true,
        ]);

        Artisan::call("panel:install");

        $this->info("¡El paquete fue reinstalado!");
    }
}
