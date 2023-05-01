<?php

namespace WeirdoPanel\Commands\Actions;

use WeirdoPanel\WeirdoPanelServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Iya30n\DynamicAcl\Providers\DynamicAclServiceProvider;

class Install extends Command
{

    protected $signature = 'panel:install';
    protected $description = 'Install panel';

    public function handle()
    {
        $this->warn("\nInstalling Admin panel ...");

        Artisan::call('vendor:publish', [
            '--provider' => WeirdoPanelServiceProvider::class,
            '--tag' => 'weirdo-panel-styles'
        ]);

        Artisan::call('vendor:publish', [
            '--provider' => WeirdoPanelServiceProvider::class,
            '--tag' => 'weirdo-panel-views'
        ]);

        Artisan::call('vendor:publish', [
            '--provider' => WeirdoPanelServiceProvider::class,
            '--tag' => 'weirdo-panel-config'
        ]);

        Artisan::call('vendor:publish', [
            '--provider' => WeirdoPanelServiceProvider::class,
            '--tag' => 'weirdo-panel-cruds'
        ]);

        Artisan::call('vendor:publish', [
            '--provider' => WeirdoPanelServiceProvider::class,
            '--tag' => 'weirdo-panel-lang'
        ]);

        Artisan::call('vendor:publish', [
            '--provider' => WeirdoPanelServiceProvider::class,
            '--tag' => 'weirdo-panel-migration'
        ]);

        Artisan::call('vendor:publish', [
            '--provider' => DynamicAclServiceProvider::class
        ]);

        Artisan::call('migrate');

        $this->line("<options=bold,reverse;fg=green>\nSe instaló weirdo panel 🎉</>\n\nCree un panel de administración increíble en menos de 5 minutos 🤓\n");
    }
}
