<?php

namespace WeirdoPanel\Http\Livewire\User;

use Livewire\Component;
use DynamicAcl\Models\Role;
use Livewire\WithFileUploads;
use WeirdoPanel\Support\Contract\UserProviderFacade;

class Create extends Component
{
    use WithFileUploads;

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

    public function updated($input)
    {
        $this->validateOnly($input);
    }

    public function getModel()
    {
        return app(config('weirdo_panel.user_model'));
    }

    public function create()
    {
        if($this->getRules())
            $this->validate();

        if ($this->selectedRoles[0] == "null")
            $this->selectedRoles = [];

        $this->dispatchBrowserEvent('show-message', ['type' => 'success', 'message' => __('CreatedMessage', ['name' => __('User') ])]);
        $userModel = $this->getModel();
        $user = $userModel->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'user_id' => auth()->id(),
        ]);
        $user->roles()->sync($this->selectedRoles);

        UserProviderFacade::makeAdmin($user->id, false);

        $this->reset();
    }

    public function render()
    {
        $this->roles = Role::where('name', '<>', 'super_admin')->get();

        return view('admin::livewire.user.create')
            ->layout('admin::layouts.app', ['title' => __('CreateTitle', ['name' => __('User') ])]);
    }
}
