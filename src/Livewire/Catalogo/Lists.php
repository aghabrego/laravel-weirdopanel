<?php

namespace WeirdoPanel\Livewire\Catalogo;

use Livewire\Component;

class Lists extends Component
{

    public function render()
    {
        return view('admin::livewire.catalogo.lists')
            ->layout('admin::layouts.life', ['title' => __('ListTitle', ['name' => __('Catalogos')])]);
    }
}
