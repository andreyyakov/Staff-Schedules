<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rota extends Model
{
    protected $table = 'rotas';
    protected $fillable = ['shop_id', 'week_commence_date'];

    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }

    public function shifts()
    {
        return $this->hasMany('App\Models\Shift', 'rota_id', 'id');
    }
}
