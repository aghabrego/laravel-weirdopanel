<?php

namespace WeirdoPanel\Http\Livewire\User;

use Livewire\Component;
use DynamicAcl\Models\Role;
use Livewire\WithFileUploads;
use WeirdoPanel\Support\Contract\OrganizationFacade;
use WeirdoPanel\Support\Contract\UserProviderFacade;

class Update extends Component
{
    use WithFileUploads;

    public $user;
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
        'password' => 'nullable|string|min:8',
    ];

    public function mount($user)
    {
        $this->roles = Role::where('name', '<>', 'super_admin')->get();
        $admin = UserProviderFacade::findUser($user);
        $this->user = $admin;
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->password = null;
        $this->selectedRoles = $admin->roles()->pluck('id');

        if (config('weirdo_panel.with_organization_model')) {
            $this->selectedOrganizations = $admin->organizations()->pluck('organizations.id');
        }
    }

    public function updated($input)
    {
        $this->validateOnly($input);
    }

    protected function getRules()
    {
        return array_merge($this->rules, ['email' => 'required|unique:users,email,' . $this->user->id]);
    }

    public function update()
    {
        if($this->getRules())
            $this->validate($this->getRules());

        if (isset($this->selectedRoles[0]) && $this->selectedRoles[0] == "null")
            $this->selectedRoles = [];

        if (isset($this->selectedOrganizations[0]) && $this->selectedOrganizations[0] == "null")
            $this->selectedOrganizations = [];

        $this->dispatchBrowserEvent('show-message', ['type' => 'success', 'message' => __('UpdatedMessage', ['name' => __('User') ]) ]);

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'roles' => $this->roles,
            'user_id' => auth()->id(),
        ]);
        $this->user->roles()->sync($this->selectedRoles);

        if (config('weirdo_panel.with_organization_model')) {
            OrganizationFacade::makeOrganization($this->user->id, $this->selectedOrganizations);
        }

        UserProviderFacade::makeAdmin($this->user->getKey(), false);
    }

    public function render()
    {
        if (config('weirdo_panel.with_organization_model')) {
            $this->organizations = OrganizationFacade::getOrganizations();
        }

        return view('admin::livewire.user.update', [
            'user' => $this->user
        ])->layout('admin::layouts.app', ['title' => __('UpdateTitle', ['name' => __('User') ])]);
    }
}
