<?php

namespace WeirdoPanel\Livewire\Role;

use Livewire\Component;
use DynamicAcl\ACL;
use DynamicAcl\Models\Role;

class Create extends Component
{
    public $name;

    public $permissionsData = [];

    public $access = [];

    public $selectedAll = [];

    protected $rules = [
        'name' => 'required|min:3|unique:roles',
        'access' => 'required'
    ];

    private function fixAccessKeys()
    {
        foreach($this->access as $key => $value) {
            unset($this->access[$key]);
            $key = str_replace('-', '.', $key);
            $this->access[$key] = is_array($value) ? array_filter($value) : $value;
        }

        return array_filter($this->access);
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
            $this->selectedAll[$dashKey] = count($selectedRoutes) == count($this->permissionsData[$key]) + 1;
        } else {
            $this->selectedAll[$dashKey] = $selectedRoutes;
        }
    }

    public function create()
    {
        $this->validate();

        try {
            Role::create(['name' => $this->name, 'permissions' => $this->fixAccessKeys()]);

            $this->dispatch('show-message', ['type' => 'success', 'message' => __('CreatedMessage', ['name' => __('Role') ])]);
        } catch (\Exception $exception){
            $this->dispatch('show-message', ['type' => 'error', 'message' => $exception->getMessage()]);
        }

        $this->reset();
    }

    public function render()
    {
        $this->permissionsData = ACL::getRoutes();

        return view('admin::livewire.role.create', ['permissions' => $this->permissionsData])
            ->layout('admin::layouts.app', ['title' => __('CreateTitle', ['name' => __('Role') ])]);
    }
}
