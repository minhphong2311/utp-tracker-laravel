<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Breaks extends Model
{
    use HasFactory;

    protected $fillable = [
        'clock_id','startbreak','endbreak','total_time'
    ];

    public function getClock()
    {
        return $this->hasOne(Clocks::class, 'id', 'clock_id');
    }
}
