<?php

namespace WeirdoPanel\Commands\UserActions;

use WeirdoPanel\Support\Contract\UserProviderFacade;
use Illuminate\Console\Command;

class GetAdmins extends Command
{

    protected $description = 'Obtener lista de administradores';
    protected $signature = 'panel:admins';

    public function handle()
    {
        $admins = UserProviderFacade::getAdmins();
        $this->warn('Listas de administradores :');
        foreach ($admins as $admin){
            $message = $admin->panelAdmin->is_superuser
                ? "• {$admin->name}: {$admin->email} ( Super Admin ✅ )"
                : "• {$admin->name}: {$admin->email}";

            $this->warn($message);
        }
    }
}
