<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StartBreakRequest;
use App\Http\Requests\EndBreakRequest;
use Illuminate\Support\Carbon;

use App\Models\Breaks;

class BreakController extends Controller
{
    public function startBreak(StartBreakRequest $request)
    {
        $validated = $request->validated();

        $data = Breaks::create($validated);

        return response()->json(['startbreak_id' => $data->id, 'msg' => 'Created successfully'], 200);
    }

    public function endBreak(EndBreakRequest $request)
    {
        $validated = $request->validated();

        $getStartBreak = Breaks::findOrFail($validated['startbreak_id']);

        if($getStartBreak->created_at->format("Y-m-d") == date("Y-m-d")){
            $startTime = Carbon::parse($getStartBreak->startbreak);
            $endTime = Carbon::parse($validated['endbreak']);
            $totalMinutes = $startTime->diffInMinutes($endTime);

            $paramEndBreak = [
                'endbreak' => $validated['endbreak'],
                'total_time' => date('H:i', mktime(0, $totalMinutes)),
            ];

            $getStartBreak->update($paramEndBreak);

            return response()->json(['msg' => 'Created successfully'], 200);
        }else{
            return response()->json(['error' => 'The clock closed at midnight. If you forgot to clock out. Contact admin for support.'], 411);
        }

    }
}
