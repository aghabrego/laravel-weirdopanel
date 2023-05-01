<?php

namespace WeirdoPanel\Commands\UserActions;

use WeirdoPanel\Support\Contract\UserProviderFacade;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{

    protected $description = 'Registrar un nuevo administrador';

    protected $signature = 'panel:add {user} {--s|super : Admin will be a super user}';

    public function handle()
    {
        $user = $this->argument('user');
        try {
            $status = UserProviderFacade::makeAdmin($user, $this->option('super'));
            $method = $status['type'] == 'success' ? 'info' : 'warn';
            $this->$method($status['message']);
        } catch (\Exception $exception) {
            $this->warn("¡Algo salió mal!\nError: ". $exception->getMessage());
        }
    }
}
