<?php

namespace WeirdoPanel\Models;

use Illuminate\Database\Eloquent\Model;

class PanelAdmin extends Model
{
    protected $table = 'panel_admins';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(config('weirdo_panel.user_model'));
    }
}
