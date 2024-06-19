<?php

return [

    // Enable whole module
    'enable' => true,

    // To prevent registration of crud routes
    'enable_crud_route' => true,

    // RTL Style , If you are using a language like Persian or Arabic change it true
    'rtl_mode' => false,

    // Package Language
    'lang' => 'en',

    // Your user Model
    'user_model' => file_exists(app_path('User.php')) ? 'App\User' : 'App\Models\User',

    // Your role model
    'role_model' => file_exists(app_path('Role.php')) ? 'App\Role' : 'App\Models\Role',

    // Your personal access token Model
    'personal_access_token_model' => file_exists(app_path('Personalaccesstoken.php')) ? 'App\Personalaccesstoken' : 'App\Models\Personalaccesstoken',

    // Model verification of organizations
    'with_organization_model' => (file_exists(app_path('Organization.php')) || file_exists(app_path('Models/Organization.php'))),

    // Organization model
    'organization_model' => file_exists(app_path('Organization.php')) ? 'App\Organization' : 'App\Models\Organization',

    // set default guard to authenticate admins
    'auth_guard' => config('auth.defaults.guard') ?? 'web',

    // How to authenticate admin
    // You may use other ways to authenticate a admin (tables or ..) you can manage it with this class
    'auth_class' => \WeirdoPanel\Support\Auth\AdminIdentifier::class,

    // With this class you can manage how to create a admin or remove it.
    'admin_provider_class' => \WeirdoPanel\Support\User\UserProvider::class,

    // With this class you can manage how to create an organization or delete it
    'organization_provider_class' => \WeirdoPanel\Support\Organization\OrganizationProvider::class,

    //The namespace of lang manager class
    'lang_manager_class' => \WeirdoPanel\Services\LangService::class,

    // it's a place where a user if not authenticated will be redirected
    'redirect_unauthorized' => '/',

    // Admin panel routes prefix
    'route_prefix' => 'admin', //  http://localhost/admin

    // Your own middlewares for easy panel routes.
    'additional_middlewares' => [],

    // Count of pagination in CRUD lists
    'pagination_count' => 20,

    // Lazy validation for Livewire components
    'lazy_mode' => true,

    // License expiration check
    'custom_remote_license_verification' => [
        'url_base' => env('APP_REMOTE_LICENSE', 'http://localhost'),
    ],
];
