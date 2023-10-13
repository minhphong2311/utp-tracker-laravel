<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Events;
use App\Models\Clocks;
use App\Models\Breaks;
use App\Http\Requests\ClockRequest;
use App\Http\Requests\BreakRequest;

class OfflineController extends Controller
{
    public function clockOffline(ClockRequest $request)
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
            $file_name = time() . '.' . $validated['clockin_photo']->extension();
            //$validated['clockin_photo']->move(public_path('uploads'), $file_name);
            $path = '/public/uploads/' . $file_name;
        }

        if($validated['clockout_photo'] && $validated['clockout_photo']->isValid()){
            $file_name = time().'.'.$validated['clockout_photo']->extension();
            //$validated['clockout_photo']->move(public_path('uploads'),$file_name);
            $path = '/public/uploads/'.$file_name;
        }

        $paramClock = [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'clockin' => $validated['clockin'],
            'clockin_photo' => $path,
            'clockin_location' => $validated['clockin_location'],
            'clockin_address' => $validated['clockin_address'],

            'clockout' => $validated['clockout'],
            'clockout_photo' => $path,
            'clockout_location' => $validated['clockout_location'],
            'clockout_address' => $validated['clockout_address'],
            'total_time' => $validated['total_time'],
        ];

        $data = Clocks::create($paramClock);

        if(isset($data->id) && isset($request->startbreak) && isset($request->endbreak) && isset($request->total_time_break)){
            foreach($request->startbreak as $key => $index){
                if(isset($index) && isset($request->endbreak[$key]) && isset($request->total_time_break[$key])){
                    $add = [
                        'clock_id' => $data->id,
                        'startbreak' => $index,
                        'endbreak' => $request->endbreak[$key],
                        'total_time' => $request->total_time_break[$key]
                    ];
                    Breaks::create($add);
                }
            }
        }

        return response()->json(['clock' => $data, 'msg' => 'Created successfully'], 200);
    }

    public function breakOffline(BreakRequest $request)
    {
        $validated = $request->validated();

        $data = Breaks::create($validated);

        return response()->json(['break' => $data, 'msg' => 'Created successfully'], 200);
    }
}
