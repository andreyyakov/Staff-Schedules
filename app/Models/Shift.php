<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shifts';
    protected $fillable = ['rota_id', 'staff_id', 'start_time', 'end_time'];

    public function breaks()
    {
        return $this->hasMany('App\Models\ShiftBreak');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }

}
