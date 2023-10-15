<?php

namespace WeirdoPanel\Support\User;

use DynamicAcl\Models\Role;
use WeirdoPanel\Support\Contract\UserProviderFacade;

class UserProvider
{

    public function makeAdmin($id, $is_super = false)
    {
        $user = $this->findUser($id);

        if ($user->panelAdmin()->exists()){
            return [
                'type' => 'error',
                'message' => '¡El usuario ya es administrador!'
            ];
        }

        $user->panelAdmin()->create([
            'is_superuser' => $is_super,
        ]);

        if($is_super)
            $this->makeSuperAdminRole($user);

        return [
            'type' => 'success',
            'message' => "Usuario '$id' se convirtió en administrador",
        ];
    }

    public function getAdmins()
    {
        return $this->getUserModel()::query()->whereHas('panelAdmin')->with('panelAdmin')->get();
    }

    public function paginateAdmins($perPage = 20)
    {
        return $this->getUserModel()::query()->whereHas('panelAdmin')->with('panelAdmin')->paginate($perPage);
    }

    public function findUser($id)
    {
        $data = $this->getUserModel()::query();
        $user = auth()->user();
        if ($user && !$user->hasPermission('fullAccess')) {
            $data->where('user_id', $user->getKey())->orWhere('id', $user->getKey());
        }

        return $data->findOrFail($id);
    }

    public function findUserForEmail($email)
    {
        return $this->getUserModel()::query()->where('email', $email)->first();
    }

    public function deleteAdmin($id)
    {
        $user = $this->findUser($id);

        $user->panelAdmin()->delete();
    }

    public function getUserModel()
    {
        return config('weirdo_panel.user_model');
    }

    public function getUserModelInstance()
    {
        return app(UserProviderFacade::getUserModel());
    }

    private function makeSuperAdminRole($user)
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], [
            'name' => 'super_admin',
            'permissions' => [
                'fullAccess' => 1
            ]
        ]);

        $role->users()->sync([$user->id]);
    }

    public function getPersonalAccessTokenModel()
    {
        return config('weirdo_panel.personal_access_token_model');
    }

    public function getPersonalAccessTokenInstance()
    {
        return app(UserProviderFacade::getPersonalAccessTokenModel());
    }

}
