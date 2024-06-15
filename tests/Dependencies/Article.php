<?php

namespace WeirdoPanelTest\Dependencies;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\Concerns\WithFactories;

class Article extends Model
{
    use WithFactories;

    protected $guarded = [];
}
