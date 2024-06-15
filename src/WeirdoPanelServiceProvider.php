<?php


namespace WeirdoPanel;

use WeirdoPanel\Commands\{Actions\PublishStubs,
    Actions\Reinstall,
    CRUDActions\MakeCreate,
    UserActions\GetAdmins,
    Actions\MakeCRUDConfig,
    CRUDActions\MakeRead,
    CRUDActions\MakeSingle,
    CRUDActions\MakeUpdate,
    Actions\DeleteCRUD,
    Actions\MakeCRUD,
    UserActions\DeleteAdmin,
    Actions\Install,
    UserActions\MakeAdmin,
    Actions\Migration,
    Actions\Uninstall};
use WeirdoPanel\Http\Middleware\isAdmin;
use WeirdoPanel\Http\Middleware\LangChanger;
use WeirdoPanel\Support\Contract\{LangManager, UserProviderFacade, AuthFacade, OrganizationFacade};
use Illuminate\{Routing\Router, Support\Facades\Blade, Support\Facades\Route, Support\ServiceProvider};
use Livewire\Livewire;
use WeirdoPanel\Models\PanelAdmin;
use WeirdoPanelTest\Dependencies\User;
use Illuminate\Database\Connection;

class WeirdoPanelServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Here we merge config with 'weirdo_panel' key
        $this->mergeConfigFrom(__DIR__ . '/../config/weirdo_panel_config.php', 'weirdo_panel');

        // Check the status of module
        if(!config('weirdo_panel.enable')) {
            return;
        }

        // Facades will be set
        $this->defineFacades();

        Connection::macro('useDatabases', function (string $databaseName) {
            /** @var \Illuminate\Database\Connection $this */

            if (strpos($databaseName, "memory:") !== false) {
                return;
            }

            $this->getPdo()->exec("USE `$databaseName`;");
            $this->setDatabaseName($databaseName);
        });
    }

    public function boot()
    {
        if(!config('weirdo_panel.enable')) {
            return;
        }

        // Here we register publishes and Commands
        if ($this->app->runningInConsole()) {
            $this->mergePublishes();
        }

        // Bind Artisan commands
        $this->bindCommands();

        // Load Views with 'admin::' prefix
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'admin');

        // Register Middleware
        $this->registerMiddlewareAlias();

        // Define routes if doesn't cached
        $this->defineRoutes();

        // Load Livewire components
        $this->loadLivewireComponent();

        // Load relationship for administrators
        $this->loadRelations();

        Blade::componentNamespace("\\WeirdoPanel\\ViewComponents", 'weirdopanel');
    }

    private function defineRoutes()
    {
        if(!$this->app->routesAreCached()) {
            $middlewares = array_merge(['web', 'isAdmin', 'LangChanger'], config('weirdo_panel.additional_middlewares'));

            $prefix = config('weirdo_panel.route_prefix');

            Route::prefix($prefix)
                ->middleware($middlewares)
                ->name(getRouteName() . '.')
                ->group(__DIR__ . '/routes.php');
        }
    }

    private function defineFacades()
    {
        AuthFacade::shouldProxyTo(config('weirdo_panel.auth_class'));
        UserProviderFacade::shouldProxyTo(config('weirdo_panel.admin_provider_class'));
        LangManager::shouldProxyTo(config('weirdo_panel.lang_manager_class'));
        OrganizationFacade::shouldProxyTo(config('weirdo_panel.organization_provider_class'));
    }

    private function registerMiddlewareAlias()
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('isAdmin', isAdmin::class);
        $router->aliasMiddleware('LangChanger', LangChanger::class);
    }

    private function loadLivewireComponent()
    {
        Livewire::component('admin::livewire.crud.single', Livewire\CRUD\Single::class);
        Livewire::component('admin::livewire.crud.create', Livewire\CRUD\Create::class);
        Livewire::component('admin::livewire.crud.lists', Livewire\CRUD\Lists::class);

        Livewire::component('admin::livewire.translation.manage', Livewire\Translation\Manage::class);

        Livewire::component('admin::livewire.role.single', Livewire\Role\Single::class);
        Livewire::component('admin::livewire.role.create', Livewire\Role\Create::class);
        Livewire::component('admin::livewire.role.update', Livewire\Role\Update::class);
        Livewire::component('admin::livewire.role.lists', Livewire\Role\Lists::class);

        Livewire::component('admin::livewire.admins.single', Livewire\Admins\Single::class);
        Livewire::component('admin::livewire.admins.update', Livewire\Admins\Update::class);

        Livewire::component('admin::livewire.user.single', Livewire\User\Single::class);
        Livewire::component('admin::livewire.user.create', Livewire\User\Create::class);
        Livewire::component('admin::livewire.user.update', Livewire\User\Update::class);
        Livewire::component('admin::livewire.user.lists', Livewire\User\Lists::class);
    }

    private function mergePublishes()
    {
        $this->publishes([__DIR__ . '/../config/weirdo_panel_config.php' => config_path('weirdo_panel.php')], 'weirdo-panel-config');

        $this->publishes([__DIR__ . '/../resources/views' => resource_path('/views/vendor/admin')], 'weirdo-panel-views');

        $this->publishes([__DIR__ . '/../resources/assets' => public_path('/assets/admin')], 'weirdo-panel-styles');

        $this->publishes([
            __DIR__ . '/../database/migrations/cruds_table.php' => base_path('/database/migrations/' . date('Y_m_d') . '_999999_create_cruds_table_weirdopanel.php'),
            __DIR__ . '/../database/migrations/panel_admins_table.php' => base_path('/database/migrations/' . date('Y_m_d') . '_999999_create_panel_admins_table_weirdopanel.php'),
        ], 'weirdo-panel-migration');

        $this->publishes([__DIR__.'/../resources/lang' => app()->langPath()], 'weirdo-panel-lang');

        $this->publishes([__DIR__.'/Commands/stub' => base_path('/stubs/panel')], 'weirdo-panel-stubs');
    }

    private function bindCommands()
    {
        $this->commands([
            MakeAdmin::class,
            DeleteAdmin::class,
            Install::class,
            MakeCreate::class,
            MakeUpdate::class,
            MakeRead::class,
            MakeSingle::class,
            MakeCRUD::class,
            DeleteCRUD::class,
            MakeCRUDConfig::class,
            GetAdmins::class,
            Migration::class,
            Uninstall::class,
            Reinstall::class,
            PublishStubs::class
        ]);
    }

    private function loadRelations()
    {
        $model = config('weirdo_panel.user_model');
        if ($this->app->runningUnitTests() && class_exists(User::class)) {
            $model = User::class;
        }

        $model::resolveRelationUsing('panelAdmin', function ($userModel){
            return $userModel->hasOne(PanelAdmin::class)->latest();
        });
    }

}
