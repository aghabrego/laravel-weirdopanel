<?php

namespace WeirdoPanel\Models;

use Illuminate\Database\Eloquent\Model;
use WeirdoPanel\Traits\CustomConnection;

class CRUD extends Model
{
    use CustomConnection;

    /**
     * @var string
     */
    protected $table = 'cruds';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setDefaultConnection();
    }

    public function scopeActive($query)
    {
        return $query->where('built', true)->where('active', true)->get();
    }
}
