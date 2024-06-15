<?php

namespace WeirdoPanel\Livewire\Personalaccess;

use Livewire\Component;

class Single extends Component
{

    public $personalaccesstoken;

    public function mount($personalaccesstoken)
    {
        $this->personalaccesstoken = $personalaccesstoken;
    }

    public function delete()
    {
        $this->personalaccesstoken->delete();
        $this->dispatchBrowserEvent('show-message', ['type' => 'error', 'message' => __('DeletedMessage', ['name' => __('PersonalAccessToken') ]) ]);
        $this->emit('personalaccesstokenDeleted');
    }

    public function render()
    {
        return view('admin::livewire.personalaccess.single')
            ->layout('admin::layouts.app');
    }
}
