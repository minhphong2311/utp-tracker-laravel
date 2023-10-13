<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Events;
use App\Models\Clocks;
use Carbon\Carbon;

use App\Http\Resources\ClockResource;

class HistoryController extends Controller
{
    public function historyDay($user_wp_id,$day)
    {
        $getUser = User::where('user_wp_id',$user_wp_id)->first();
        if($getUser){
            return ClockResource::collection(Clocks::where('user_id',$getUser->id)->where('total_time','<>','')->whereDate('created_at', $day)->get());
        }
        return response()->json(['data' => []]);
    }

    public function historyWeek($user_wp_id,$startday,$endday)
    {
        $getUser = User::where('user_wp_id',$user_wp_id)->first();
        if($getUser){
            $data = ClockResource::collection(Clocks::where('user_id',$getUser->id)->where('total_time','<>','')->whereDate('created_at','>=',$startday)->whereDate('created_at','<=',$endday)->get());
            if($data){
                $sum_minutes = 0;
                foreach($data as $time){
                    // $explodedTime = array_map('intval', explode(':', $time['total_time'] ));
                    // $minutes = $explodedTime[0]*60+$explodedTime[1];
                    // $sum_minutes += $minutes;

                    $explodedTime = array_map('intval', explode(':', $time['total_time'] ));
                    $sum_minutes += $explodedTime[0]*60+$explodedTime[1];
                }
                // $hours = floor($sum_minutes/60);
                // $minutes = floor($sum_minutes % 60);
                // $sumTime = $hours.':'.$minutes;
                $sumTime = floor($sum_minutes/60).':'.str_pad(floor($sum_minutes % 60), 2, '0', STR_PAD_LEFT);
            }
            return response()->json(['total_hours' => $sumTime,'data' => $data]);
        }
        return response()->json(['data' => []]);
    }

    public function historyMonth($user_wp_id,$year,$month)
    {
        $getUser = User::where('user_wp_id',$user_wp_id)->first();
        if($getUser){
            $data = ClockResource::collection(Clocks::where('user_id',$getUser->id)->where('total_time','<>','')->whereMonth('created_at', $month)->whereYear('created_at', $year)->get());
            if($data){
                $sum_minutes = 0;
                foreach($data as $time){
                    $explodedTime = array_map('intval', explode(':', $time['total_time'] ));
                    $sum_minutes += $explodedTime[0]*60+$explodedTime[1];
                }
                $sumTime = floor($sum_minutes/60).':'.str_pad(floor($sum_minutes % 60), 2, '0', STR_PAD_LEFT);
            }
            return response()->json(['total_hours' => $sumTime,'data' => $data]);
        }
        return response()->json(['data' => []]);
    }
}
