<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;

use App\Models\User;
use App\Models\Clocks;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $view = ['title' => 'Employee', 'type' => 'employee'];
        view()->share($view);
    }

    public function index(Request $request)
    {
        $param = $request->all();
        $page = '20';

        $data = User::query();
        if (isset($param['search_text'])) {
            $data->where('name', 'LIKE', '%' . $param['search_text'] . '%');
        }
        $data = $data->where('level','Employee')->with('getClocks')->orderBy('created_at', 'desc')->paginate($page);
        if (isset($param['search_text'])) {
            $data->appends(['search_text' => $param['search_text']]);
        }

        return view('admincp.employee.index', compact('data'));
    }

    public function show(Request $request, $id)
    {
        $param = $request->all();
        $data = User::where('id', $id)->first();

        $clock = Clocks::query();
        $page = '20';
        if (isset($param['search_text'])) {
            switch ($param['search_text']) {
                case "Weekly":
                    $clock->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case "Bi-Weekly":
                    $clock->whereBetween('created_at', [Carbon::now()->startOfWeek()->subWeeks(1), Carbon::now()->endOfWeek()]);
                    break;
                case "Monthly":
                    $clock->whereMonth('created_at', '=', date('m'));
                    break;
                case "Yearly":
                    $clock->whereYear('created_at', '=', date('Y'));
                }

        }

        if (isset($param['date'])) {
            $day = explode(" - ", $param['date']);
            $fromDate = Carbon::createFromFormat('d M Y', $day[0])->format('Y-m-d');
            $toDate = Carbon::createFromFormat('d M Y', $day[1])->format('Y-m-d');

            $clock->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        }


        $clockAll = $clock->where('user_id', $id)->get();
        $sum_minutes = 0;
        $result_earned = 0;
        $sumTime = "00:00";
        $earn = 0;
        $earnId = [];
        if($clockAll){
            foreach($clockAll as $index):
                $explodedTime = '';
                if($index['total_time'] != ''){
                    $explodedTime = array_map('intval', explode(':', $index['total_time'] ));
                    $sum_minutes += $explodedTime[0]*60+$explodedTime[1];
                }
                if($index['earned_amount']){
                    $result_earned += $index['earned_amount'];
                }else{
                    $earn++;
                    $earnId[] = $index['id'];
                }
            endforeach;
            $sumTime = sprintf('%02d:%02d',(floor($sum_minutes/60)), floor($sum_minutes % 60));
        }

        $data['total_time'] = $sumTime;
        $data['total_earned'] = $result_earned;
        $data['earn'] = $earn;
        $data['earn_id'] = implode(',',$earnId);
        // dd($data['earn_id']);

        $clock = $clock->with('getEvent','getUser')->orderBy('created_at', 'desc')->paginate($page);


        if (isset($param['search_text'])) {
            $clock->appends(['search_text' => $param['search_text']]);
        }
        if (isset($param['date'])) {
            $clock->appends(['date' => $param['date']]);
        }

        return view('admincp.employee.edit', compact('data','clock'));
    }

    public function update(Request $request, $id)
    {
        $getData = User::where('id', $id)->first();
        $data = $request->all();

        if ($getData->count())
            $getData->update($data);

        return redirect()->back()->with('update', 'Update successfully!');
    }

    public function pay(Request $request, $id){
        $param = $request->all();
        $clocks_id = explode(',', $param['clocks_id']);
        $user = User::where('id', $id)->first();
        $getClocks = Clocks::where('user_id', $id)->whereIn('id',$clocks_id)->get();
        if($getClocks && $clocks_id && $user){
            foreach($getClocks as $clock){
                if($clock['total_time']){
                    $total_money = '';$sum_minutes = '';
                    $explodedTime = array_map('intval', explode(':', $clock['total_time'] ));
                    $sum_minutes = $explodedTime[0]*60+$explodedTime[1];
                    $total_money = intval($sum_minutes) * ($user->hourly_rate / 60);
                    Clocks::where("id", $clock['id'])->update(["hourly_rate" => $user->hourly_rate, "earned_amount" => $total_money]);
                }
            }
            return redirect()->back()->with('update', 'Pay successfully!');
        }

        return redirect()->back()->with('update', 'Pay failed!');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function export(Request $request, $id)
    {
        $param = $request->all();
        $param['user'] = $id;

        $user = User::where('id', $id)->first();

        $param['date'] = $request->input('date');
        if(isset($param['date'])){
            $day = explode(" - ", $param['date']);
            $nameFile = Carbon::createFromFormat('d M Y', $day[0])->format('Y.m.d').'-'.Carbon::createFromFormat('d M Y', $day[1])->format('Y.m.d');
        }elseif(isset($param['search_text'])){
            $nameFile = $param['search_text'];
        }else{
            $nameFile = 'All';
        }

        return Excel::download(
            new UsersExport($param),
            $nameFile. ' - User '.$user->name.'.csv', //csv,xlsx
            \Maatwebsite\Excel\Excel::CSV,
            [
                'Content-Type' => 'text/csv',
            ]
        );
    }
}
