<?php

namespace App\Exports;

use App\Models\Clocks;
use App\Models\Events;
use App\Models\User;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class ClocksExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $param;

    function __construct($param)
    {
        $this->param = $param;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // if (isset($this->param['date'])) {
        //     $day = explode(" - ", $this->param['date']);
        //     $fromDate = Carbon::createFromFormat('d M Y', $day[0])->format('Y-m-d');
        //     $toDate = Carbon::createFromFormat('d M Y', $day[1])->format('Y-m-d');

        //     $data = Clocks::whereDate('created_at', '>=', $fromDate)
        //         ->whereDate('created_at', '<=', $toDate)
        //         ->groupBy('user_id')
        //         ->selectRaw('SEC_TO_TIME( SUM(time_to_sec(`total_time`)))  as total_time,
        //             SUM(`earned_amount`) as earned_amount,
        //             SUM(`bonus_pay`) as bonus_pay, user_id')
        //         ->get();
        // } else {
        //     $data = Clocks::groupBy('user_id')
        //         ->selectRaw('SEC_TO_TIME( SUM(time_to_sec(`total_time`)))  as total_time,
        //             SUM(`earned_amount`) as earned_amount,
        //             SUM(`bonus_pay`) as bonus_pay, user_id')
        //         ->get();
        // }


        $data = Clocks::query();

        if (isset($this->param['date'])) {
            $day = explode(" - ", $this->param['date']);
            $fromDate = Carbon::createFromFormat('d M Y', $day[0])->format('Y-m-d');
            $toDate = Carbon::createFromFormat('d M Y', $day[1])->format('Y-m-d');

            $data->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        }


        if (isset($this->param['search_text'])) {
            $search_text = $this->param['search_text'];
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

        $data = $data->with('getUser','getEvent')->orderBy('user_id', 'asc')->orderBy('event_id', 'desc')->get();

        $row = [];
        $users = [];

        foreach ($data as $index) {
            $users[$index->user_id][] = array(
                'event' => $index->getEvent->event_name,
                'name' => $index->getUser->name,
                'created_at' => $index->created_at->format('m/d/Y'),
                'clockin' => $index->clockin,
                'clockout' => $index->clockout,
                'total_time' => $index->total_time,
                'earned_amount' => round(floatval($index->earned_amount), 2),
                'bonus_pay' => round(floatval($index->bonus_pay), 2)
            );
        }
        if ($users) {
            foreach ($users as $index) {
                $user = '';
                $total_earned = 0;
                $total_pay = 0;
                foreach ($index as $item) {
                    $row[] = array(
                        '0' => trim($item['event']),
                        '1' => trim($item['name']),
                        '2' => $item['created_at'],
                        '3' => trim($item['clockin']),
                        '4' => trim($item['clockout']),
                        '5' => trim($item['total_time']),
                        '6' => ($item['earned_amount'] > 0) ? '$ '.$item['earned_amount'] : '',
                        '7' => ($item['bonus_pay'] > 0) ? '$ '.$item['bonus_pay'] : ''
                    );
                    $user = trim($item['name']);
                    $total_earned += $item['earned_amount'];
                    $total_pay += $item['bonus_pay'];
                }
                $row[] = array(
                    '0' => 'Total Pay',
                    '1' => $user,
                    '2' => '',
                    '3' => '',
                    '4' => '',
                    '5' => '',
                    '6' => ($total_earned > 0) ? '$ '.$total_earned : '',
                    '7' => ($total_pay > 0) ? '$ '.$total_pay : ''
                );
            }
        }

        return (collect($row));
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ["Events", "Employees", "Working Days", "Clock In", "Clock Out", "Total Time(hh:mm)", "Total Earned", "Other Pay"];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $event->sheet->styleCells(
                    'A1:H1',
                    [
                        //Set font style
                        'font' => [
                            'size'      =>  13,
                            'bold'      =>  true,
                            'color' => ['rgb' => 'ffffff'],
                        ],

                        //Set background style
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'rgb' => '000000',
                            ]
                        ],

                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],

                    ]
                );

            },
        ];
    }
}
