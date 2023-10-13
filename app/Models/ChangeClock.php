<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeClock extends Model
{
    use HasFactory;

    protected $fillable = [
        'clock_id','clockin','clockout','total_time',
        'change_clockin','change_clockout','change_total_time','comment',
        'status','approver'
    ];

    public function getClock()
    {
        return $this->hasOne(Clocks::class, 'id', 'clock_id')->with('getEvent','getUser');
    }

    public function getUser()
    {
        return $this->hasOne(User::class, 'id', 'approver');
    }
}
