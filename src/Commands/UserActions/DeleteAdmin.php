<?php

namespace WeirdoPanel\Commands\UserActions;

use WeirdoPanel\Support\Contract\UserProviderFacade;
use Illuminate\Console\Command;

class DeleteAdmin extends Command
{

    protected $signature = 'panel:remove {user} {--f|force}';
    protected $description = 'Eliminar un administrador con ID de usuario';

    public function handle()
    {
        $user = $this->argument('user');

        if($this->askResult($user)){
            UserProviderFacade::deleteAdmin($user);
            $this->info('El administrador fue eliminado con éxito');
            return;
        }

        $this->warn('El proceso fue cancelado');
    }

    public function askResult($user)
    {
        if($this->option('force')) {
            return true;
        }

        return $this->confirm("¿Quieres eliminar{$user} de la administración", 'yes');
    }
}
