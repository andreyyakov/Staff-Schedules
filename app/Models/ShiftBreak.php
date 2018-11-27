<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftBreak extends Model
{
    protected $table = 'shift_breaks';
    protected $fillable = ['shift_id', 'start_time', 'end_time'];
}
