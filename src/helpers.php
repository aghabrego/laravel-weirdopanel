<?php

use WeirdoPanel\Contracts\CRUDComponent;

if (!function_exists('getRealpath')) {
    /**
     * @param string $path
     * @return string|false
     */
    function getRealpath(string $path = __DIR__)
    {
        return realpath($path);
    }
}

if (!function_exists('getRouteName')) {
    function getRouteName() {
        $routeName = config('weirdo_panel.route_prefix');
        $routeName = trim($routeName, '/');
        $routeName = str_replace('/', '.', $routeName);
        return $routeName;
    }
}

if (!function_exists('getCrudConfig')) {
    function getCrudConfig($name) {
        $namespace = "\\App\\CRUD\\{$name}Component";

        if (!file_exists(getRealpath(app_path("/CRUD/{$name}Component.php"))) or !class_exists($namespace)) {
            abort(403, "Class with {$namespace} namespace doesn't exist");
        }

        $instance = app()->make($namespace);

        if (!$instance instanceof CRUDComponent) {
            abort(403, "{$namespace} should implement CRUDComponent interface");
        }

        return $instance;
    }
}

if (!function_exists('crud')) {
    function crud($name) {
        return \WeirdoPanel\Models\CRUD::query()->where('name', $name)->first();
    }
}

if (!function_exists('hasPermission')) {
    function hasPermission($routeName, $withAcl, $withPolicy = false, $entity = []) {
        $showButton = true;

        if ($withAcl) {
            if (!auth()->user()->hasPermission($routeName, $withAcl, false, $entity)) {
                $showButton = false;
            } else if ($withPolicy && !auth()->user()->hasPermission($routeName, $withAcl, true, $entity)) {
                $showButton = false;
            }
        }

        return $showButton;
    }
}
