<?php

namespace WeirdoPanel\Commands\Actions;

use WeirdoPanel\WeirdoPanelServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Migration extends Command
{

    protected $signature = 'panel:migration';
    protected $description = 'Publicar archivo de migraciones';

    public function handle()
    {
        Artisan::call('vendor:publish', [
            '--provider' => WeirdoPanelServiceProvider::class,
            '--tag' => 'weirdo-panel-migrations'
        ]);

        $this->line("<options=bold,reverse;fg=green>\nSe publicaron las migraciones</>");
    }
}
