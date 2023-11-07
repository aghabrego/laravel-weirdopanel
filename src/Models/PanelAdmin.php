<?php

namespace WeirdoPanel\Models;

use Illuminate\Database\Eloquent\Model;
use WeirdoPanel\Traits\CustomConnection;

class PanelAdmin extends Model
{
    use CustomConnection;

    /**
     * @var string
     */
    protected $table = 'panel_admins';

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

    public function user()
    {
        return $this->belongsTo(config('weirdo_panel.user_model'));
    }
}
