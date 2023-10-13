<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Breaks;
use Carbon\Carbon;

class Clocks extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','event_id',
        'clockin','clockin_photo','clockin_location','clockin_address',
        'clockout','clockout_photo','clockout_location','clockout_address',
        'total_time','earned_amount','bonus_pay','hourly_rate','comment','created_at','updated_at'
    ];

    public function getUser()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getEvent()
    {
        return $this->hasOne(Events::class, 'id', 'event_id');
    }

    public function getBreaks()
    {
        return $this->hasMany(Breaks::class, 'clock_id', 'id');
    }

    public function getTotalBreak()
    {
        $getBreaks = Breaks::where('clock_id',$this->id)->get();
        $totalMinutes = '';
        $result = '';
        if($getBreaks){
            foreach($getBreaks as $key => $index):
                if($key == 0){
                    $totalMinutes = Carbon::parse($index['startbreak'])->diffInMinutes($index['endbreak']?$index['endbreak']:$this->clockout);
                }else{
                    $totalMinutes += Carbon::parse($index['startbreak'])->diffInMinutes($index['endbreak']?$index['endbreak']:$this->clockout);
                }
            endforeach;
            $result = date('H:i', mktime(0, intval($totalMinutes)));
        }
        return $result;
    }

    public function getChangeClock()
    {
        return $this->hasMany(ChangeClock::class, 'clock_id', 'id');
    }

    public function getReceipts()
    {
        return $this->hasMany(Receipts::class, 'clock_id', 'id')->orderBy('id','desc');
    }
}
