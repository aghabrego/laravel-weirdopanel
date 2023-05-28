<?php


namespace WeirdoPanelTest\Dependencies;
use Orchestra\Testbench\Concerns\WithFactories;

class User extends \Illuminate\Foundation\Auth\User
{
    use WithFactories;

    protected $guarded = [];

    protected $casts = ['is_superuser'];
    public $timestamps = false;

    public function hasPermission($routeName, $withAcl, $withPolicy = false, $entity = [])
    {
        return hasPermission($routeName, $withAcl, $withPolicy, $entity);
    }
}
