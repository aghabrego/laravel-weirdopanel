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
use WeirdoPanel\Support\Contract\{LangManager, UserProviderFacade, AuthFacade};
use Illuminate\{Routing\Router, Support\Facades\Blade, Support\Facades\Route, Support\ServiceProvider};
use Livewire\Livewire;
use WeirdoPanel\Models\PanelAdmin;
use WeirdoPanelTest\Dependencies\User;

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

            Route::prefix(config('weirdo_panel.route_prefix'))
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
    }

    private function registerMiddlewareAlias()
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('isAdmin', isAdmin::class);
        $router->aliasMiddleware('LangChanger', LangChanger::class);
    }

    private function loadLivewireComponent()
    {
        Livewire::component('admin::livewire.crud.single', Http\Livewire\CRUD\Single::class);
        Livewire::component('admin::livewire.crud.create', Http\Livewire\CRUD\Create::class);
        Livewire::component('admin::livewire.crud.lists', Http\Livewire\CRUD\Lists::class);

        Livewire::component('admin::livewire.translation.manage', Http\Livewire\Translation\Manage::class);

        Livewire::component('admin::livewire.role.single', Http\Livewire\Role\Single::class);
        Livewire::component('admin::livewire.role.create', Http\Livewire\Role\Create::class);
        Livewire::component('admin::livewire.role.update', Http\Livewire\Role\Update::class);
        Livewire::component('admin::livewire.role.lists', Http\Livewire\Role\Lists::class);

        Livewire::component('admin::livewire.admins.single', Http\Livewire\Admins\Single::class);
        Livewire::component('admin::livewire.admins.update', Http\Livewire\Admins\Update::class);
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
        $model = !$this->app->runningUnitTests() ? config('weirdo_panel.user_model') : User::class;

        $model::resolveRelationUsing('panelAdmin', function ($userModel){
            return $userModel->hasOne(PanelAdmin::class)->latest();
        });
    }

}
