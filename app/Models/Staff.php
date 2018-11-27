<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';
    protected $fillable = ['first_name', 'surname', 'shop_id'];


    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }

    public function shifts()
    {
        return $this->hasMany('App\Models\Shift');
    }
}
