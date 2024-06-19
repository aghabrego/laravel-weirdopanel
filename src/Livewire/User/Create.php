<?php

namespace WeirdoPanel\Livewire\User;

use Livewire\Component;
use Livewire\WithFileUploads;
use WeirdoPanel\Support\Contract\OrganizationFacade;
use WeirdoPanel\Support\Contract\UserProviderFacade;

class Create extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $password;
    public $roles = [];
    public $selectedRoles = [];
    public $organizations = [];
    public $selectedOrganizations = [];

    protected $rules = [
        'name' => 'required',
        'email' => 'required|unique:users,email',
        'password' => 'required|min:8',
    ];

    public function updated($input)
    {
        $this->validateOnly($input);
    }

    public function getModel()
    {
        return app(config('weirdo_panel.user_model'));
    }

    public function getRoleModel()
    {
        return app(config('weirdo_panel.role_model'));
    }

    public function create()
    {
        if($this->getRules())
            $this->validate();

        if (isset($this->selectedRoles[0]) && $this->selectedRoles[0] == "null")
            $this->selectedRoles = [];

        if (isset($this->selectedOrganizations[0]) && $this->selectedOrganizations[0] == "null")
            $this->selectedOrganizations = [];

        $this->dispatch('show-message', type: 'success', message: __('CreatedMessage', ['name' => __('User') ]));
        $userModel = $this->getModel();
        $user = $userModel->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'user_id' => auth()->id(),
        ]);
        $user->roles()->sync($this->selectedRoles);

        if (config('weirdo_panel.with_organization_model')) {
            OrganizationFacade::makeOrganization($user->id, $this->selectedOrganizations);
        }

        UserProviderFacade::makeAdmin($user->id, false);

        $this->reset();
    }

    public function render()
    {
        $role = $this->getRoleModel();
        $this->roles = $role->all();

        if (config('weirdo_panel.with_organization_model')) {
            $this->organizations = OrganizationFacade::allOrganizations();
        }

        return view('admin::livewire.user.create')
            ->layout('admin::layouts.app', ['title' => __('CreateTitle', ['name' => __('User') ])]);
    }
}
