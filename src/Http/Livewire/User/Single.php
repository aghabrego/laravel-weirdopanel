<?php

namespace WeirdoPanel\Http\Livewire\User;

use Livewire\Component;

class Single extends Component
{

    public $user;

    public function mount($User)
    {
        $this->user = $User;
    }

    public function delete()
    {
        $this->user->delete();
        $this->dispatchBrowserEvent('show-message', ['type' => 'error', 'message' => __('DeletedMessage', ['name' => __('User') ]) ]);
        $this->emit('userDeleted');
    }

    public function render()
    {
        return view('livewire.admin.user.single')
            ->layout('admin::layouts.app');
    }
}
