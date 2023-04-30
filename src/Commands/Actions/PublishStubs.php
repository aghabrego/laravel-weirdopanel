<?php

namespace WeirdoPanel\Commands\Actions;

use WeirdoPanel\WeirdoPanelServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PublishStubs extends Command
{

    protected $signature = 'panel:publish';
    protected $description = 'Publicar stubs de paquete';

    public function handle()
    {
        Artisan::call('vendor:publish', [
            '--provider' => WeirdoPanelServiceProvider::class,
            '--tag' => 'weirdo-panel-stubs'
        ]);

        $this->info("Stubs se publicó con éxito");
    }
}
