<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table = 'shops';
    protected $fillable = ['name'];

    public function rotas()
    {
        return $this->hasMany('App\Models\Rota');
    }

    public function staff()
    {
        return $this->hasMany('App\Models\Staff');
    }

}
