<?php

namespace WeirdoPanel\Livewire\CRUD;

use Livewire\Component;
use WeirdoPanel\Models\CRUD;
use Illuminate\Support\Facades\Artisan;

class Single extends Component
{

    public $crud;

    public function mount(CRUD $crud)
    {
        $this->crud = $crud;
    }

    public function delete()
    {
        Artisan::call('panel:delete', [
            'name' => $this->crud->name,
            '--force' => true,
        ]);

        $this->crud->delete();
        $this->dispatch('show-message', ['type' => 'error', 'message' => __('DeletedMessage', ['name' => __('CRUD') ] )]);
        $this->dispatch('crudUpdated');
    }

    public function build()
    {
        Artisan::call('panel:crud', [
            'name' => $this->crud->name,
            '--force' => true,
        ]);

        $this->crud->update([
            'built' => true
        ]);

        $this->dispatch('show-message', ['type' => 'success', 'message' => __('CRUD creado con éxito') ] );
        $this->dispatch('crudUpdated');
    }

    public function inactive()
    {
        $this->crud->update([
            'active' => false
        ]);

        $this->dispatch('show-message', ['type' => 'success', 'message' => __('CRUD fue desactivado') ] );
        $this->dispatch('crudUpdated');
    }

    public function active()
    {
        $this->crud->update([
            'active' => true
        ]);

        $this->dispatch('show-message', ['type' => 'success', 'message' => __('CRUD fue activado') ] );
        $this->dispatch('crudUpdated');
    }

    public function render()
    {
        return view('admin::livewire.crud.single')
            ->layout('admin::layouts.app');
    }
}
