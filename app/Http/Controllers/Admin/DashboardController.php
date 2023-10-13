<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Carbon;

use App\Models\Clocks;
use App\Models\Events;
use App\Models\User;

class DashboardController extends Controller
{
    public function __construct()
    {
        $view = ['title'=>'Clocks','type'=>'clocks'];
        view()->share($view);
    }

    public function dashboard(Request $request)
    {
        $param = $request->all();
        $page = '20';

        $data = Clocks::query();

        if (isset($param['date'])) {
            $day = explode(" - ", $param['date']);
            $fromDate = Carbon::createFromFormat('d M Y', $day[0])->format('Y-m-d');
            $toDate = Carbon::createFromFormat('d M Y', $day[1])->format('Y-m-d');

            $data->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        }


        if (isset($param['search_text'])) {
            $search_text = $request->input('search_text');
            $inEvents = [];
            $inUsers = [];
            $findEvent = Events::where('event_name', 'LIKE', '%'. $search_text . '%')->get();
            if($findEvent){
                foreach($findEvent as $item){
                    $inEvents[] = $item['id'];
                }
            }

            $findUser = User::where('name', 'LIKE', '%'. $search_text . '%')->get();
            if($findUser){
                foreach($findUser as $item){
                    $inUsers[] = $item['id'];
                }
            }

            $data->where(function ($query) use ($inEvents,$inUsers) {
                $query->where('event_id',$inEvents)
                      ->orWhere('user_id',$inUsers);
            });
        }

        $data = $data->with('getUser','getEvent','getReceipts')->orderBy('created_at', 'desc')->paginate($page);
        if (isset($param['date'])) {
            $data->appends(['date' => $param['date']]);
        }
        if (isset($param['search_text'])) {
            $data->appends(['search_text' => $param['search_text']]);
        }

        return view('admincp.dashboard', compact('data'));
    }

}
