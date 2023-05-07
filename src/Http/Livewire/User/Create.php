<?php

namespace WeirdoPanel\Http\Livewire\User;

use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $password;
    public $roles;
    
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

        $this->dispatchBrowserEvent('show-message', ['type' => 'success', 'message' => __('CreatedMessage', ['name' => __('User') ])]);
        $userModel = $this->getModel();
        $userModel->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $this->reset();
    }

    public function render()
    {
        return view('livewire.admin.user.create')
            ->layout('admin::layouts.app', ['title' => __('CreateTitle', ['name' => __('User') ])]);
    }
}
