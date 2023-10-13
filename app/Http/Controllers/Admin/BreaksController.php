<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

use App\Models\Breaks;
use App\Models\Clocks;

class BreaksController extends Controller
{
    public function __construct()
    {
        $view = ['title' => 'Breaks', 'type' => 'breaks'];
        view()->share($view);
    }

    public function show($id)
    {
        $data = Breaks::where('id', $id)->first();

        return view('admincp.breaks.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $getData = Breaks::where('id', $id)->first();
        $data = $request->all();

        $validator = Validator::make($data, [
            'startbreak' => 'required',
            'endbreak' => 'required',
        ]);

        if (strtotime($getData->getClock['clockin']) > strtotime($data['startbreak'])) {
            $validator->after(function ($validator) {
                $validator->errors()->add('startbreak', 'Must be greater than the ClockIn time');
            });
        }
        if (strtotime($data['startbreak']) > strtotime($data['endbreak'])) {
            $validator->after(function ($validator) {
                $validator->errors()->add('endbreak', 'Must be greater than the start time');
            });
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data['startbreak'] = date("H:i", strtotime($data['startbreak']));
        $data['endbreak'] = date("H:i", strtotime($data['endbreak']));

        $startTime = Carbon::parse($data['startbreak']);
        $endTime = Carbon::parse($data['endbreak']);
        $totalMinutes = $startTime->diffInMinutes($endTime);

        $data['total_time'] =  date('H:i', mktime(0, $totalMinutes));

        if ($getData->count())
            $getData->update($data);

        if($getData->getClock['hourly_rate']){
            $getClock = Clocks::where('id',$getData->clock_id)->first();

            $totalTime = '';
            $startTime = Carbon::parse($getClock->clockin);
            $endTime = Carbon::parse($getClock->clockout);
            $totalTime = $startTime->diffInMinutes($endTime);

            $breaks = Breaks::where('clock_id', $getClock->id)->get();
            if ($breaks) :
                foreach ($breaks as $key => $index) :
                    if ($key == 0) {
                        $totalTime -= Carbon::parse($index['startbreak'])->diffInMinutes($index['endbreak']?$index['endbreak']:$getClock['clockout']);
                    } else {
                        $totalTime -= Carbon::parse($index['startbreak'])->diffInMinutes($index['endbreak']?$index['endbreak']:$getClock['clockout']);
                    }
                endforeach;
            endif;

            $update['earned_amount'] = round(floatval(intval($totalTime) * ($getClock->hourly_rate / 60)), 2);

            if ($getClock->count())
                $getClock->update($update);
        }

        return redirect()->back()->with('update', 'Update successfully!');
    }
}
