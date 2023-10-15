<?php

namespace App\Http\Livewire\Admin\Personalaccesstoken;

use Livewire\Component;

class Single extends Component
{

    public $personalaccesstoken;

    public function mount($PersonalAccessToken)
    {
        $this->personalaccesstoken = $PersonalAccessToken;
    }

    public function delete()
    {
        $this->personalaccesstoken->delete();
        $this->dispatchBrowserEvent('show-message', ['type' => 'error', 'message' => __('DeletedMessage', ['name' => __('PersonalAccessToken') ]) ]);
        $this->emit('personalaccesstokenDeleted');
    }

    public function render()
    {
        return view('livewire.admin.personalaccesstoken.single')
            ->layout('admin::layouts.app');
    }
}
