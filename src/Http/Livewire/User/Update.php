<?php

namespace App\Http\Livewire\Admin\User;

use Livewire\Component;
use Livewire\WithFileUploads;

class Update extends Component
{
    use WithFileUploads;

    public $user;
    public $name;
    public $email;
    public $password;
    public $roles;

    protected $rules = [
        'name' => 'required',
        'email' => 'required|unique:users,email',
        'password' => 'sometimes|required|min:8',
    ];

    public function mount($User){
        $this->user = $User;
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->password = $this->user->password;
        $this->roles = $this->user->roles;
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

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'roles' => $this->roles,
            'user_id' => auth()->id(),
        ]);
    }

    public function render()
    {
        return view('livewire.admin.user.update', [
            'user' => $this->user
        ])->layout('admin::layouts.app', ['title' => __('UpdateTitle', ['name' => __('User') ])]);
    }
}
