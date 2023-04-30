<?php

namespace WeirdoPanel\Support\User;

use App\Models\User;
use Iya30n\DynamicAcl\Models\Role;

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
        return $this->getUserModel()::query()->findOrFail($id);
    }

    public function deleteAdmin($id)
    {
        $user = $this->findUser($id);

        $user->panelAdmin()->delete();
    }

    private function getUserModel()
    {
        return config('weirdo_panel.user_model') ?? User::class;
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

}
