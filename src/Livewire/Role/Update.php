<?php

namespace WeirdoPanel\Livewire\Role;

use DynamicAcl\ACL;
use DynamicAcl\Models\Role;
use Livewire\Component;

class Update extends Component
{
    public $role;

    public $name;

    public $permissions = [];

    public $access = [];

    public $selectedAll = [];

    protected $rules = [
        'name' => 'min:3'
    ];

    public function mount(Role $role)
    {
        $this->role = $role;

        $this->name = $role->name;

        $this->permissions = ACL::getRoutes();

        $this->setSelectedAccess($role->permissions);
    }

    /**
     * this method checks if whole checkboxes checked, set value true for SelectAll checkbox
     *
     * @param string $key
     *
     * @param string $dashKey
     */
    public function checkSelectedAll($key, $dashKey)
    {
        $selectedRoutes = is_array($this->access[$dashKey]) ? array_filter($this->access[$dashKey]) : $this->access[$dashKey];

        // we don't have delete route in cruds but we have a button for it. that's why i added 1
        if (is_array($selectedRoutes)) {
            $this->selectedAll[$dashKey] = count($selectedRoutes) == count($this->permissions[$key]) + 1;
        } else {
            $this->selectedAll[$dashKey] = $selectedRoutes;
        }
    }

    public function updated($input)
    {
        $this->validateOnly($input);
    }

    public function update()
    {
        if ($this->getRules())
            $this->validate();

        if ($this->role->is_super_admin()) {
            $this->dispatch('show-message', ['type' => 'error', 'message' => __('CannotUpdateMessage', ['name' => __('Role')])]);
            return;
        }

        $this->role->update([
            'name' => $this->name,
            'permissions' => $this->getSelectedAccess()
        ]);

        $this->dispatch('show-message', ['type' => 'success', 'message' => __('UpdatedMessage', ['name' => __('Role')])]);
    }

    public function render()
    {
        return view('admin::livewire.role.update', [
            'role' => $this->role,
        ])->layout('admin::layouts.app', ['title' => __('UpdateTitle', ['name' => __('Role')])]);
    }

    private function setSelectedAccess($rolePermissions)
    {
        foreach($rolePermissions as $key => $value) { 
            $dashKey = str_replace('.', '-', $key);
            $value = is_array($value) ? array_filter($value) : $value; 

            if (empty($value))
                continue;

            $this->access[$dashKey] = $value;

            $this->checkSelectedAll($key, $dashKey);
        }
    }

    private function getSelectedAccess()
    {
        $access = $this->access;
        
        foreach($access as $key => $value) {
            unset($access[$key]);
            $key = str_replace('-', '.', $key);
            $access[$key] = is_array($value) ? array_filter($value) : $value;
        }

        return $access;
    }
}
