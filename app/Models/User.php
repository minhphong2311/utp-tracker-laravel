<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_wp_id',
        'active',
        'level',
        'hourly_rate'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getClocks()
    {
        return $this->hasMany(Clocks::class)->select(['id', 'user_id', 'total_time', 'earned_amount']);
    }

    public function getTotalHours()
    {
        // $getClocks = Clocks::where('user_id',$this->id)->get();
        $getClocks = $this->hasMany(Clocks::class)->get();
        $sum_minutes = 0;
        $sumTime = "00:00";
        if($getClocks){
            foreach($getClocks as $index):
                if($index['total_time'] != ''){
                    $explodedTime = array_map('intval', explode(':', $index['total_time'] ));
                    $sum_minutes += $explodedTime[0]*60+$explodedTime[1];
                }
            endforeach;
            $sumTime = sprintf('%02d:%02d',(floor($sum_minutes/60)), floor($sum_minutes % 60));
        }
        return $sumTime;
    }

    public function getTotalEarned()
    {
        // $getClocks = Clocks::where('user_id',$this->id)->get();
        $getClocks = $this->hasMany(Clocks::class)->get();
        $result = 0;
        if($getClocks){
            foreach($getClocks as $index):
                if($index['earned_amount']){
                    $result += $index['earned_amount'];
                }
            endforeach;
        }
        return round(floatval($result), 2);
    }
}
