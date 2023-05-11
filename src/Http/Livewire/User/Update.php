<?php

namespace WeirdoPanel\Http\Livewire\User;

use Livewire\Component;
use DynamicAcl\Models\Role;
use Livewire\WithFileUploads;
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

    protected $rules = [
        'name' => 'required',
        'email' => 'required|unique:users,email',
        'password' => 'sometimes|required|min:8',
    ];

    public function mount($user)
    {
        $this->roles = Role::where('name', '<>', 'super_admin')->get();
        $admin = UserProviderFacade::findUser($user);
        $this->user = $admin;
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->password = $this->user->password;
        $this->selectedRoles = $admin->roles()->pluck('id');
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

        $this->dispatchBrowserEvent('show-message', ['type' => 'success', 'message' => __('UpdatedMessage', ['name' => __('User') ]) ]);

        $user = $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'roles' => $this->roles,
            'user_id' => auth()->id(),
        ]);
        $user->roles()->sync($this->selectedRoles);

        UserProviderFacade::makeAdmin($user->id, false);
    }

    public function render()
    {
        return view('admin::livewire.user.update', [
            'user' => $this->user
        ])->layout('admin::layouts.app', ['title' => __('UpdateTitle', ['name' => __('User') ])]);
    }
}
