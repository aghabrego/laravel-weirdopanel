<?php

namespace WeirdoPanel\Commands\Actions;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use WeirdoPanel\Support\Contract\LangManager;

class Uninstall extends Command
{

    protected $signature = 'panel:uninstall {--f|force : Force mode}';
    protected $description = 'Desinstalar el panel';

    public function handle()
    {
        $status = $this->option('force') ? true : $this->confirm("¿De verdad quieres desinstalar el panel? ? (Todos los archivos y componentes serán eliminados.)", true);

        if (!$status) {
            $this->info("El proceso fue cancelado");
            return;
        }

        // Delete folders and files which WeirdoPanel published
        $this->deleteFiles();

        // Drop tables which has been created by WeirdoPanel
        $this->dropTables();

        $this->deleteMigrations();

        $this->info("¡Todos los archivos y componentes fueron eliminados!");
    }

    private function dropTables()
    {
        Schema::dropIfExists('cruds');
        Schema::dropIfExists('panel_admins');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }

    private function deleteFiles()
    {
        File::deleteDirectory(app_path('Http/Livewire/Admin'));
        File::deleteDirectory(app_path('CRUD'));
        File::deleteDirectory(resource_path('views/livewire/admin'));
        File::deleteDirectory(resource_path('views/vendor/admin'));
        File::deleteDirectory(resource_path('cruds'));
        File::deleteDirectory(public_path('assets/admin'));
        File::delete(config_path('weirdo_panel.php'));
        File::delete(config_path('dynamicACL.php'));
        File::delete(LangManager::getFiles());
    }

    private function deleteMigrations()
    {
        $migrationFiles = File::glob(database_path('migrations/*999999*.php'));

        File::delete($migrationFiles);

        DB::table('migrations')
            ->where('migration', 'like', '%999999%')
            ->delete();
    }
}
