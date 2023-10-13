<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ChangeClock;
use App\Models\Clocks;
use App\Models\Events;
use App\Models\User;

class ChangeClocksController extends Controller
{
    public function __construct()
    {
        $view = ['title'=>'Change Time Requests','type'=>'change-clocks'];
        view()->share($view);
    }

    public function index(Request $request)
    {
        $param = $request->all();
        $page = '20';
        $data = ChangeClock::query();

        $clocks = Clocks::query();

        if (isset($param['search_text'])) {
            $search_text = $request->input('search_text');
            $inEvents = [];
            $inUsers = [];
            $findEvent = Events::where('event_name', 'LIKE', '%'. $search_text . '%')->get();
            if($findEvent){
                foreach($findEvent as $item){
                    $inEvents[] = $item['id'];
                }
                $clocks->where('event_id',$inEvents);
            }

            $findUser = User::where('name', 'LIKE', '%'. $search_text . '%')->get();
            if($findUser){
                foreach($findUser as $item){
                    $inUsers[] = $item['id'];
                }
                $clocks->orWhere('event_id',$inUsers);
            }

            $clocks = $clocks->orderBy('created_at', 'desc')->get();

            if($clocks){
                $inClocks = [];
                foreach($clocks as $item){
                    $inClocks[] = $item['id'];
                }
                $data->where('clock_id',$inClocks);
            }
        }

        $data = $data->with('getClock')->orderBy('updated_at', 'desc')->paginate($page);
        if (isset($param['search_text'])) {
            $data->appends(['search_text' => $param['search_text']]);
        }

        return view('admincp.change-clocks.index', compact('data'));
    }

    public function show($id)
    {
        $data = ChangeClock::where('id', $id)->with('getClock','getUser')->first();

        return view('admincp.change-clocks.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $getData = ChangeClock::where('id', $id)->first();
        $data = $request->all();

        if($data['status']=='Approved'){
            $getClocks = Clocks::where('id',$getData['clock_id'])->first();

            $explodedTime = array_map('intval', explode(':', $getData['change_total_time'] ));
            $sum_minutes = $explodedTime[0]*60+$explodedTime[1];

            $total_money = '';$sum_minutes = '';
            if($getClocks->hourly_rate){
                $explodedTime = array_map('intval', explode(':', $getData['change_total_time'] ));
                $sum_minutes = $explodedTime[0]*60+$explodedTime[1];
                $total_money = round(floatval(intval($sum_minutes) * ($getClocks->hourly_rate / 60)), 2);
            }

            $param = [
                'clockin' => $getData['change_clockin'],
                'clockout' => $getData['change_clockout'],
                'total_time' => $getData['change_total_time'],
                'earned_amount' => $total_money,
            ];
            if ($getClocks->count())
                $getClocks->update($param);
        }
        if($data['status']=='Rejected'){
            $getClocks = Clocks::where('id',$getData['clock_id'])->first();

            $total_money = '';$sum_minutes = '';
            if($getClocks->hourly_rate){
                $explodedTime = array_map('intval', explode(':', $getData['total_time'] ));
                $sum_minutes = $explodedTime[0]*60+$explodedTime[1];
                $total_money = round(floatval(intval($sum_minutes) * ($getClocks->hourly_rate / 60)), 2);
            }

            $param = [
                'clockin' => $getData['clockin'],
                'clockout' => $getData['clockout'],
                'total_time' => $getData['total_time'],
                'earned_amount' => $total_money,
            ];
            if ($getClocks->count())
                $getClocks->update($param);
        }
        $data['approver'] = auth()->user()->id;

        if ($getData->count())
            $getData->update($data);

        return redirect()->back()->with('update', 'Update successfully!');
    }
}
