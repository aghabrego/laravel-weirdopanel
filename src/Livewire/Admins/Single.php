<?php

namespace WeirdoPanel\Livewire\Admins;

use WeirdoPanel\Support\Contract\UserProviderFacade;
use Livewire\Component;

class Single extends Component
{

    public $admin;

    public function mount($admin)
    {
        $this->admin = $admin;
    }

    public function delete()
    {
        if (auth()->id() == $this->admin->id) {
            $this->dispatch('show-message', ['type' => 'error', 'message' => __('CannotDeleteMessage', ['name' => __('Admin')])]);
            return;
        }

        $this->admin->roles()->sync([]);

        UserProviderFacade::deleteAdmin($this->admin->id);

        $this->dispatch('show-message', ['type' => 'error', 'message' => __('DeletedMessage', ['name' => __('Admin') ] )]);
        $this->dispatch('adminsUpdated');
    }

    public function render()
    {
        return view('admin::livewire.admins.single')
            ->layout('admin::layouts.app');
    }
}
