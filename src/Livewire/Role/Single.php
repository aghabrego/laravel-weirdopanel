<?php

namespace WeirdoPanel\Livewire\Role;

use Livewire\Component;
use DynamicAcl\Models\Role;

class Single extends Component
{

    public $role;

    public function mount(Role $role)
    {
        $this->role = $role;
    }

    public function delete()
    {
        if ($this->role->is_super_admin()) {
            $this->dispatch('show-message', ['type' => 'error', 'message' => __('CannotDeleteMessage', ['name' => __('Role')])]);
            return;
        }

        $this->role->users()->sync([]);
        
        $this->role->delete();

        $this->dispatch('show-message', ['type' => 'error', 'message' => __('DeletedMessage', ['name' => __('Role') ] )]);
        $this->dispatch('roleUpdated');
    }

    public function render()
    {
        return view('admin::livewire.role.single')
            ->layout('admin::layouts.app');
    }
}
