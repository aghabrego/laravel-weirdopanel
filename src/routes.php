<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use WeirdoPanel\Support\Contract\UserProviderFacade;

Route::view('/', "admin::home")->name('home');

Route::post('/logout', function (){
    auth()->logout();
    return redirect(config('weirdo_panel.redirect_unauthorized'));
})->name('logout');

if (\Illuminate\Support\Facades\Schema::hasTable('cruds')) {
    foreach (\WeirdoPanel\Models\CRUD::active() as $crud) {
        $crudConfig = getCrudConfig($crud->name);
        $name = ucfirst($crud->name);
        $component = "App\\Http\\Livewire\\Admin\\$name";

        $middleware = [];
        if ($crud->with_acl) {
            $middleware[] = "dynamicAcl";

            if ($crud->with_policy)
                $middleware[] = "authorize";
        }

        Route::prefix($crud->route)->middleware($middleware)->name("{$crud->route}.")->group(function () use ($component, $crud, $crudConfig) {

            if (@class_exists("$component\\Read")) {
                Route::get('/', "$component\\Read")->name('read');
            }

            if (@$crudConfig->create and @class_exists("$component\\Create")) {
                Route::get('/create', "$component\\Create")->name('create');
            }

            if (@$crudConfig->update and @class_exists("$component\\Update")) {
                Route::get('/update/{' . $crud->name . '}', "$component\\Update")->name('update');
            }

        });
    }
}

Route::prefix('crud')->middleware('dynamicAcl')->name('crud.')->group(function (){
    Route::get('/', \WeirdoPanel\Http\Livewire\CRUD\Lists::class)->name('lists');
    Route::get('/create', \WeirdoPanel\Http\Livewire\CRUD\Create::class)->name('create');
});

Route::get('setLang', function (){
    $lang = request()->get('lang');

    session()->put('weirdopanel_lang', $lang);
    App::setLocale($lang);

    return redirect()->back();
})->name('setLang');

Route::get('setOrganization', function () {
    $org = request()->get('org');

    $user = UserProviderFacade::findUser(Auth()->id());

    if (method_exists($user, config('weirdo_panel.user_set_organization.put'))) {
        $user->{config('weirdo_panel.user_set_organization.put')}($org);
    }

    if (config('weirdo_panel.user_organization') !== false) {
        $user->{config('weirdo_panel.user_organization')} = $org;
        $user->save();
    }

    if (method_exists($user, config('weirdo_panel.user_set_connection'))) {
        $user->{config('weirdo_panel.user_set_connection')}($org);
    }

    if (empty($org)) {
        return redirect(config('weirdo_panel.route_prefix'));
    }

    return redirect()->back();
})->name('setOrganization');

Route::get('translation', \WeirdoPanel\Http\Livewire\Translation\Manage::class)
    ->middleware('dynamicAcl')
    ->name('translation');

Route::prefix('role')->middleware('dynamicAcl')->name('role.')->group(function (){
    Route::get('/', \WeirdoPanel\Http\Livewire\Role\Lists::class)->name('lists');
    Route::get('/create', \WeirdoPanel\Http\Livewire\Role\Create::class)->name('create');
    Route::get('/update/{role}', \WeirdoPanel\Http\Livewire\Role\Update::class)->name('update');
});

Route::prefix('users')->middleware('dynamicAcl')->name('users.')->group(function (){
    Route::get('/', \WeirdoPanel\Http\Livewire\User\Lists::class)->name('lists');
    Route::get('/create', \WeirdoPanel\Http\Livewire\User\Create::class)->name('create');
    Route::get('/update/{user}', \WeirdoPanel\Http\Livewire\User\Update::class)->name('update');
});

Route::prefix('admins')->middleware('dynamicAcl')->name('admins.')->group(function (){
    Route::get('/', \WeirdoPanel\Http\Livewire\Admins\Lists::class)->name('lists');
    Route::get('/create', \WeirdoPanel\Http\Livewire\Admins\Create::class)->name('create');
    Route::get('/update/{admin}', \WeirdoPanel\Http\Livewire\Admins\Update::class)->name('update');
});

Route::prefix('personalaccesstokens')->middleware('dynamicAcl')->name('personalaccesstokens.')->group(function (){
    Route::get('/', \WeirdoPanel\Http\Livewire\Personalaccess\Lists::class)->name('lists');
    Route::get('/create', \WeirdoPanel\Http\Livewire\Personalaccess\Create::class)->name('create');
});

Route::prefix('catalogos')->middleware('dynamicAcl')->name('catalogos.')->group(function (){
    Route::get('/', \WeirdoPanel\Http\Livewire\Catalogo\Lists::class)->name('lists');
});
