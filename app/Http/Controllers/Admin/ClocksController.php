<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

use App\Exports\ClocksExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Clocks;
use App\Models\Events;
use App\Models\User;
use App\Models\Breaks;

class ClocksController extends Controller
{
    public function __construct()
    {
        $view = ['title' => 'Clocks', 'type' => 'clocks'];
        view()->share($view);
    }

    public function index(Request $request)
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
            $findEvent = Events::where('event_name', 'LIKE', '%' . $search_text . '%')->get();
            if ($findEvent) {
                foreach ($findEvent as $item) {
                    $inEvents[] = $item['id'];
                }
            }

            $findUser = User::where('name', 'LIKE', '%' . $search_text . '%')->get();
            if ($findUser) {
                foreach ($findUser as $item) {
                    $inUsers[] = $item['id'];
                }
            }

            $data->where(function ($query) use ($inEvents, $inUsers) {
                $query->where('event_id', $inEvents)
                    ->orWhere('user_id', $inUsers);
            });
        }

        $data = $data->with('getUser','getEvent','getReceipts')->orderBy('created_at', 'desc')->paginate($page);
        if (isset($param['date'])) {
            $data->appends(['date' => $param['date']]);
        }
        if (isset($param['search_text'])) {
            $data->appends(['search_text' => $param['search_text']]);
        }

        return view('admincp.clocks.index', compact('data'));
    }

    public function create()
    {
        return view('admincp.clocks.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'event_wp_id' => 'required',
            'event_name' => 'required',

            'user_wp_id' => 'required',
            'name' => 'required',
            'email' => 'required',
            'created_at' => 'required',

            'clockin' => 'required',
            'clockin_address' => 'required',
            'clockin_location' => 'required',

            'clockout' => 'required',
            'clockout_address' => 'required',
            'clockout_location' => 'required',

            'clockin_photo' => 'mimes:jpeg,jpg,png|max:1024',
            'clockout_photo' => 'mimes:jpeg,jpg,png|max:1024'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        if ($data['user_wp_id']) {
            $getUser = User::where('user_wp_id', $data['user_wp_id'])->first();
            if ($getUser) {
                $user = $getUser;
            } else {
                $paramUser = [
                    'user_wp_id' => $data['user_wp_id'],
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make(Str::random(5)),
                    'active' => 0,
                    'level' => 'Employee'
                ];
                $user = User::create($paramUser);
            }
        }

        if ($data['event_wp_id']) {
            $getEvent = Events::where('event_wp_id', $data['event_wp_id'])->first();
            if ($getEvent) {
                $event = $getEvent;
            } else {
                $paramEvent = [
                    'event_wp_id' => $data['event_wp_id'],
                    'event_name' => $data['event_name']
                ];
                $event = Events::create($paramEvent);
            }
        }

        $clockin_photo = '';
        if (!is_null($request->file('clockin_photo'))) {
            $file_name = Str::random(10) . '_' . time() . '.' . $request->file('clockin_photo')->extension();
            $request->file('clockin_photo')->move(public_path('uploads'), $file_name);
            $clockin_photo = '/public/uploads/' . $file_name;
        }
        $clockout_photo = '';
        if (!is_null($request->file('clockout_photo'))) {
            $file_name = Str::random(10) . '_' . time() . '.' . $request->file('clockout_photo')->extension();
            $request->file('clockout_photo')->move(public_path('uploads'), $file_name);
            $clockout_photo = '/public/uploads/' . $file_name;
        }

        $data['clockin'] = date("H:i", strtotime($data['clockin']));
        $data['clockout'] = date("H:i", strtotime($data['clockout']));


        $totalTime = '';
        $startTime = Carbon::parse($data['clockin']);
        $endTime = Carbon::parse($data['clockout']);
        $totalTime = $startTime->diffInMinutes($endTime);

        $total_time = date('H:i', mktime(0, intval($totalTime)));

        $total_money = '';
        if ($user->hourly_rate) {
            $total_money = round(floatval(intval($totalTime) * ($user->hourly_rate / 60)), 2);
        }

        $paramClock = [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'clockin' => $data['clockin'],
            'clockin_photo' => $clockin_photo,
            'clockin_location' => $data['clockin_location'],
            'clockin_address' => $data['clockin_address'],

            'clockout' => $data['clockout'],
            'clockout_photo' => $clockout_photo,
            'clockout_location' => $data['clockout_location'],
            'clockout_address' => $data['clockout_address'],

            'hourly_rate' => $user->hourly_rate,
            'total_time' => $total_time,
            'earned_amount' => $total_money,
            'comment' => $data['comment'],
            'created_at' => $data['created_at'] . ' ' . $data['clockin'] . ':00',
            'updated_at' => $data['created_at'] . ' ' . $data['clockin'] . ':00'
        ];

        Clocks::create($paramClock);

        return redirect('admincp');
    }

    public function event(Request $request)
    {
        $data = $request->all();

        $client = array(
            "client_id" => env('UTPRODUCTS_API_CLIENT_ID'),
            "client_secret" => env('UTPRODUCTS_API_CLIENT_SECRET'),
            "grant_type" => "password",
            "username" => env('UTPRODUCTS_API_USERNAME'),
            "password" => env('UTPRODUCTS_API_PASSWORD'),
        );
        $header_client = array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
        );
        $make_call = $this->callAPI('POST', 'https://utproducts.com/wp-json/api/v1/token', http_build_query($client), $header_client);
        if (!$make_call) {
            $result['message'] = 'An error occurred while fetching access token from UTPRODUCTS1';
            return response()->json($result, 500);
        }
        $token = json_decode($make_call, true);
        if (!$token || !isset($token['access_token'])) {
            $result['message'] = 'An error occurred while fetching access token from UTPRODUCTS2';
            return response()->json($result, 500);
        }

        $header_event = array(
            'Content-Type: application/json',
            'Authorization: ' . $token['token_type'] . ' ' . $token['access_token']
        );
        $call_event = $this->callAPI('GET', 'https://utproducts.com/wp-json/utpem/v1/current-events?page=1&s=' . $data['q']['term'], false, $header_event);
        if (!$call_event) {
            $result['message'] = 'An error occurred while pushing data to UTPRODUCTS1';
            return response()->json($result, 500);
        }
        $event = json_decode($call_event, true);
        if (!$event) {
            $result['message'] = 'An error occurred while pushing data to UTPRODUCTS2';
            return response()->json($result, 500);
        }

        return response()->json($event['data'], 200);
    }

    public function user(Request $request)
    {
        $data = $request->all();

        $client = array(
            "client_id" => env('UTPRODUCTS_API_CLIENT_ID'),
            "client_secret" => env('UTPRODUCTS_API_CLIENT_SECRET'),
            "grant_type" => "password",
            "username" => env('UTPRODUCTS_API_USERNAME'),
            "password" => env('UTPRODUCTS_API_PASSWORD'),
        );
        $header_client = array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
        );
        $make_call = $this->callAPI('POST', 'https://utproducts.com/wp-json/api/v1/token', http_build_query($client), $header_client);
        if (!$make_call) {
            $result['message'] = 'An error occurred while fetching access token from UTPRODUCTS';
            return response()->json($result, 500);
        }
        $token = json_decode($make_call, true);
        if (!$token || !isset($token['access_token'])) {
            $result['message'] = 'An error occurred while fetching access token from UTPRODUCTS';
            return response()->json($result, 500);
        }

        $header_user = array(
            'Content-Type: application/json',
            'Authorization: ' . $token['token_type'] . ' ' . $token['access_token']
        );
        $call_user = $this->callAPI('GET', 'https://utproducts.com/wp-json/wp/v2/users?context=edit&_fields=id,name,email&search=' . $data['q']['term'], false, $header_user);
        if (!$call_user) {
            $result['message'] = 'An error occurred while pushing data to UTPRODUCTS1';
            return response()->json($result, 500);
        }
        $user = json_decode($call_user, true);
        if (!$user) {
            $result['message'] = 'An error occurred while pushing data to UTPRODUCTS2';
            return response()->json($result, 500);
        }
        return response()->json($user, 200);
    }

    public function show($id)
    {
        $data = Clocks::with('getReceipts','getBreaks','getEvent','getUser')->where('id', $id)->first();

        return view('admincp.clocks.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $getData = Clocks::where('id', $id)->first();
        $data = $request->all();

        $validator = Validator::make($data, [
            'event_wp_id' => 'required',
            'user_wp_id' => 'required',

            'created_at' => 'required',
            'clockin_photo' => 'mimes:jpeg,jpg,png|max:1024',
            'clockout_photo' => 'mimes:jpeg,jpg,png|max:1024'
        ]);


        if (strtotime($data['clockin']) > strtotime($data['clockout'])) {
            $validator->after(function ($validator) {
                $validator->errors()->add('clockout', 'Must be greater than ClockIn time');
            });
        }

        $BreakClockIn = Breaks::where('clock_id', $id)->orderBy('id', 'asc')->first();
        if ($BreakClockIn) {
            if (strtotime($BreakClockIn['startbreak']) < strtotime($data['clockin'])) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('clockin', 'Must be less than Break time');
                });
            }
        }

        $BreakClockOut = Breaks::where('clock_id', $id)->orderBy('id', 'desc')->first();
        if ($BreakClockOut) {
            if (strtotime($BreakClockOut['endbreak'] ? $BreakClockOut['endbreak'] : $BreakClockOut['startbreak']) > strtotime($data['clockout'])) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('clockout', 'Must be greater than Break time');
                });
            }
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($data['user_wp_id']) {
            $getUser = User::where('user_wp_id', $data['user_wp_id'])->first();
            if ($getUser) {
                $user = $getUser;
            } else {
                $paramUser = [
                    'user_wp_id' => $data['user_wp_id'],
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make(Str::random(5)),
                    'active' => 0,
                    'level' => 'Employee'
                ];
                $user = User::create($paramUser);
            }
        }

        if ($data['event_wp_id']) {
            $getEvent = Events::where('event_wp_id', $data['event_wp_id'])->first();
            if ($getEvent) {
                $event = $getEvent;
            } else {
                $paramEvent = [
                    'event_wp_id' => $data['event_wp_id'],
                    'event_name' => $data['event_name']
                ];
                $event = Events::create($paramEvent);
            }
        }

        if (!is_null($request->file('clockin_photo'))) {
            $file_name = Str::random(10) . '_' . time() . '.' . $request->file('clockin_photo')->extension();
            $request->file('clockin_photo')->move(public_path('uploads'), $file_name);
            $data['clockin_photo'] = '/public/uploads/' . $file_name;
        }
        if (!is_null($request->file('clockout_photo'))) {
            $file_name = Str::random(10) . '_' . time() . '.' . $request->file('clockout_photo')->extension();
            $request->file('clockout_photo')->move(public_path('uploads'), $file_name);
            $data['clockout_photo'] = '/public/uploads/' . $file_name;
        }

        $data['clockin'] = date("H:i", strtotime($data['clockin']));
        $data['clockout'] = date("H:i", strtotime($data['clockout']));

        if ($data['hourly_rate']) {
            $totalTime = '';
            $startTime = Carbon::parse($data['clockin']);
            $endTime = Carbon::parse($data['clockout']);
            $totalTime = $startTime->diffInMinutes($endTime);

            if ($getData->getBreaks) :
                foreach ($getData->getBreaks as $key => $index) :
                    if ($key == 0) {
                        $totalTime -= Carbon::parse($index['startbreak'])->diffInMinutes($index['endbreak'] ? $index['endbreak'] : $data['clockout']);
                    } else {
                        $totalTime -= Carbon::parse($index['startbreak'])->diffInMinutes($index['endbreak'] ? $index['endbreak'] : $data['clockout']);
                    }
                endforeach;
            endif;
            $data['total_time'] = date('H:i', mktime(0, intval($totalTime)));
            $data['earned_amount'] = round(floatval(intval($totalTime) * ($data['hourly_rate'] / 60)), 2);
        }

        $data['event_id'] = $event->id;
        $data['user_id'] = $user->id;
        // $data['hourly_rate'] = $user->hourly_rate;
        $data['created_at'] = $data['created_at'] . ' ' . $data['clockin'] . ':00';

        if ($getData->count())
            $getData->update($data);

        return redirect()->back()->with('update', 'Update successfully!');
    }

    public function delete($id)
    {

        $data = Clocks::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('delete', 'Delete successfully!');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function export(Request $request)
    {
        $param = $request->all();
        $param['date'] = $request->input('date');
        if ($param['date']) {
            $day = explode(" - ", $param['date']);
            $nameFile = Carbon::createFromFormat('d M Y', $day[0])->format('Y.m.d') . '-' . Carbon::createFromFormat('d M Y', $day[1])->format('Y.m.d');
        } else {
            $nameFile = 'All';
        }

        return Excel::download(
            new ClocksExport($param),
            $nameFile . ' - Clocks.csv', //csv,xlsx
            \Maatwebsite\Excel\Excel::CSV,
            [
                'Content-Type' => 'text/csv',
            ]
        );
    }


    function callAPI($method, $url, $data, $header)
    {
        $curl = \curl_init();
        switch ($method) {
            case 'GET':
                break;
            case "POST":
                \curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    \curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                if ($data)
                    $url = \sprintf("%s?%s", $url, \http_build_query($data));
        }

        // OPTIONS:
        \curl_setopt($curl, CURLOPT_URL, $url);
        \curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        \curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        // EXECUTE:
        $result = \curl_exec($curl);
        if (!$result) {
            die("Connection Failure");
        }
        \curl_close($curl);
        return $result;
    }
}
