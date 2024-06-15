<?php

namespace WeirdoPanel\Livewire\User;

use Livewire\Component;

class Single extends Component
{

    public $user;

    public function mount($user)
    {
        $this->user = $user;
    }

    public function delete()
    {
        $this->user->delete();
        $this->dispatchBrowserEvent('show-message', ['type' => 'error', 'message' => __('DeletedMessage', ['name' => __('User') ]) ]);
        $this->emit('userDeleted');
    }

    public function render()
    {
        return view('admin::livewire.user.single')
            ->layout('admin::layouts.app');
    }
}
