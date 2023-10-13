<?php

namespace App\Exports;

use App\Models\Clocks;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class UsersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
        $clock = Clocks::query();

        if (isset($this->param['search_text'])) {
            switch ($this->param['search_text']) {
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

        if (isset($this->param['date'])) {
            $day = explode(" - ", $this->param['date']);
            $fromDate = Carbon::createFromFormat('d M Y', $day[0])->format('Y-m-d');
            $toDate = Carbon::createFromFormat('d M Y', $day[1])->format('Y-m-d');

            $clock->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        }

        $data = $clock->with('getUser','getEvent')->where('user_id', $this->param['user'])->orderBy('event_id', 'desc')->get();

        $row = [];
        $events = [];

        foreach ($data as $index) {
            $events[$index->event_id][] = array(
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

        if ($events) {
            foreach ($events as $index) {
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
