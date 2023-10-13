<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ClockInRequest;
use App\Http\Requests\ClockOutRequest;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

use App\Models\User;
use App\Models\Events;
use App\Models\Clocks;
use App\Models\Breaks;

class ClockController extends Controller
{
    public function clockIn(ClockInRequest $request)
    {

        $validated = $request->validated();

        if ($validated['user_wp_id']) {
            $getUser = User::where('user_wp_id', $validated['user_wp_id'])->first();
            if ($getUser) {
                $user = $getUser;
            } else {
                $paramUser = [
                    'user_wp_id' => $validated['user_wp_id'],
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make(Str::random(5)),
                    'active' => 0,
                    'level' => 'Employee'
                ];
                $user = User::create($paramUser);
            }
        }

        if ($validated['event_wp_id']) {
            $getEvent = Events::where('event_wp_id', $validated['event_wp_id'])->first();
            if ($getEvent) {
                $event = $getEvent;
            } else {
                $paramEvent = [
                    'event_wp_id' => $validated['event_wp_id'],
                    'event_name' => $validated['event_name']
                ];
                $event = Events::create($paramEvent);
            }
        }

        if ($validated['clockin_photo'] && $validated['clockin_photo']->isValid()) {
            $file_name = Str::random(10).'_'.time() . '.' . $validated['clockin_photo']->extension();
            $validated['clockin_photo']->move(public_path('uploads'), $file_name);
            $path = '/public/uploads/' . $file_name;
        }

        $paramClockIn = [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'clockin' => $validated['clockin'],
            'clockin_photo' => $path,
            'clockin_location' => $validated['clockin_location'],
            'clockin_address' => $validated['clockin_address'],
            'hourly_rate' => $user->hourly_rate,
            'created_at' => $validated['created_at'],
            'updated_at' => $validated['created_at']
        ];

        $data = Clocks::create($paramClockIn);

        return response()->json(['clockin_id' => $data->id, 'msg' => 'Created successfully'], 200);
    }

    public function clockOut(ClockOutRequest $request)
    {
        $validated = $request->validated();
        $getclockIn = Clocks::findOrFail($validated['clockin_id']);
        if($getclockIn->created_at->format("Y-m-d") == explode(' ',$validated['updated_at'])[0]){
            $totalTime = '';
            $total_time = '';
            $startTime = Carbon::parse($getclockIn->clockin);
            $endTime = Carbon::parse($validated['clockout']);
            $totalTime = $startTime->diffInMinutes($endTime);

            $breaks = Breaks::where('clock_id', $getclockIn->id)->get();
            if ($breaks) :
                foreach ($breaks as $key => $index) :
                    if ($key == 0) {
                        $totalTime -= Carbon::parse($index['startbreak'])->diffInMinutes($index['endbreak']?$index['endbreak']:$validated['clockout']);
                    } else {
                        $totalTime -= Carbon::parse($index['startbreak'])->diffInMinutes($index['endbreak']?$index['endbreak']:$validated['clockout']);
                    }
                endforeach;
            endif;
            $total_time = date('H:i', mktime(0, intval($totalTime)));

            if ($validated['clockout_photo'] && $validated['clockout_photo']->isValid()) {
                $file_name = Str::random(10).'_'.time() . '.' . $validated['clockout_photo']->extension();
                $validated['clockout_photo']->move(public_path('uploads'), $file_name);
                $path = '/public/uploads/' . $file_name;
            }

            $total_money = '';
            if($getclockIn->hourly_rate){
                $total_money = round(floatval(intval($totalTime) * ($getclockIn->hourly_rate / 60)), 2);
            }

            $paramClockOut = [
                'clockout' => $validated['clockout'],
                'clockout_photo' => $path,
                'clockout_location' => $validated['clockout_location'],
                'clockout_address' => $validated['clockout_address'],
                'total_time' => $total_time,
                'earned_amount' => $total_money,
                'updated_at' => $validated['updated_at']
            ];

            $getclockIn->update($paramClockOut);

            return response()->json(['msg' => 'Created successfully'], 200);
        }else{
            return response()->json(['error' => 'The clock closed at midnight. If you forgot to clock out. Contact admin for support.'], 411);
        }

    }
}
