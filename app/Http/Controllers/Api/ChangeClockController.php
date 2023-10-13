<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ChangeClockRequest;
use Illuminate\Support\Carbon;

use App\Models\ChangeClock;
use App\Models\Clocks;
use App\Models\Breaks;


class ChangeClockController extends Controller
{
    public function changeClock(ChangeClockRequest $request)
    {
        $validated = $request->validated();

        $getClock = Clocks::findOrFail($validated['clock_id']);

        $totalTime = '';
        $startTime = Carbon::parse($validated['change_clockin']);
        $endTime = Carbon::parse($validated['change_clockout']);
        $totalTime = $startTime->diffInMinutes($endTime);

        $breaks = Breaks::where('clock_id', $getClock->id)->get();
        if ($breaks) :
            foreach ($breaks as $key => $index) :
                if ($key == 0) {
                    $totalTime -= Carbon::parse($index['startbreak'])->diffInMinutes($index['endbreak'] ? $index['endbreak'] : $validated['change_clockout']);
                } else {
                    $totalTime -= Carbon::parse($index['startbreak'])->diffInMinutes($index['endbreak'] ? $index['endbreak'] : $validated['change_clockout']);
                }
            endforeach;
        endif;

        $totalTime = date('H:i', mktime(0, intval($totalTime)));

        if ($getClock) {
            $validated['clockin'] = $getClock['clockin'];
            $validated['clockout'] = $getClock['clockout'];
            $validated['total_time'] = $getClock['total_time'];
        }

        $validated['change_total_time'] = $totalTime;
        $validated['status'] = 'Requested';
        $validated['approver'] = null;

        $ChangeClock = ChangeClock::where('clock_id', $validated['clock_id'])->first();
        if ($ChangeClock) {
            $ChangeClock->update($validated);
            return response()->json(['msg' => 'Updated successfully'], 200);
        } else {
            ChangeClock::create($validated);
            return response()->json(['msg' => 'Created successfully'], 200);
        }
    }

    public function changeClockCancell($id)
    {
        $data = ChangeClock::where('id', $id)->where('status', '<>', 'Approved')->first();
        if ($data) {
            $data->delete();
            return response()->json(['msg' => 'Cancelled successfully'], 204);
        } else {
            return response()->json(['msg' => "Time changes Approved, can't delete"], 403);
        }
    }
}
