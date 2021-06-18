<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

/**
* Класс для генерации отчётов по потреблению за год и конкретный месяц
*
* @author  Nikita Khmyrov
*/
class ReportController extends Controller
{
    private $months = [
        '1' => "январь",
        '2' => "февраль",
        '3' => "март",
        '4' => "апрель",
        '5' => "май",
        '6' => "июнь",
        '7' => "июль",
        '8' => "август",
        '9' => "сентябрь",
        '10' => "октябрь",
        '11' => "ноябрь",
        '12' => "декабрь"
    ];

    private $englishMonths = [
        '1' => "january",
        '2' => "february",
        '3' => "march",
        '4' => "april",
        '5' => "may",
        '6' => "june",
        '7' => "july",
        '8' => "august",
        '9' => "september",
        '10' => "october",
        '11' => "november",
        '12' => "december"
    ];

    /**
     * Вычисляет расход ресурсов за последние дни для конкретного городка
     *
     * @param [\Illuminate\Http\Response] $request входящий запрос с необходимыми для отбора данными:
     * array:4 [
     *     "year" => "2020"
     *     "month" => "10"
     *     "divisionName" => "Военная академия связи имени С. М. Будённого"
     *     "cityName" => "Военный городок №5"
     *     "sector_id" => "2"
     * ]
     * @return html view страницу с генериованным отчётом - "Справка о расходе энергоресурсов воинской части"
     */
    public function show(Request $request)
    {
        $report = $request->all();

        $monthNumber = $report['month'];
        $report['monthName'] = $this->months[$report['month']];
        $report['englishMonthName'] = $this->englishMonths[$report['month']];

        $report_obj = $this->get_month_consumptions_by_sector($report['sector_id'], $report['year'], $monthNumber);

        return view('pages.report', compact(['report', 'report_obj']));
    }

    /**
     * Вычисляет расход ресурсов за последние дни для всех городков
     *
     * @param [\Illuminate\Http\Response] $request входящий запрос с необходимыми для отбора данными:
     * array:4 [
     *      "_token" => "vaiIzZzcI3N1TN4GfogbAHuH36AjWuM7DCVRUBtq"
     *      "year" => "2020"
     *      "month" => "10"
     *      "object_id" => "1"
     * ]
     * @return html view страницу с генериованным отчётом - "Справка о расходе энергоресурсов воинской части"
     */
    public function show_object(Request $request)
    {
        $report = $request->all();

        $monthNumber = $report['month'];
        $report['monthName'] = $this->months[$report['month']];
        $report['englishMonthName'] = $this->englishMonths[$report['month']];

        $object = \App\Models\MiltaryObject::find($report['object_id']);
        $total = 0;

        foreach ($object->sectors as $sector) {
            $sector->report = $this->get_month_consumptions_by_sector($sector->id, $report['year'],$monthNumber);
            $total += $sector->report['total'];
        }

        $total_str = number_format($total, 0, ",", " ") . " руб. " . round(100 * fmod($total, 1)) . " коп.";
        
        return view('pages.report_object', compact(['object', 'report', 'total_str']));
    }

    /**
     * Запрос в БД для суммы всех показаний за конкретный месяц
     * для конкретного военного городка
     * 
     * @return void
     */
    private function get_month_consumptions_by_sector($sectorId, $year, $numberOfMonth){
        $date = Carbon::createFromDate($year, $numberOfMonth, null, null);
        $startOfMonth=$date->startOfMonth();
        $parseStartOfMonth = Carbon::parse($startOfMonth)->format('Y-m-d').' 00:00:00';
        $endOfMonth=$date->endOfMonth();
        $parseEndOfMonth = Carbon::parse($endOfMonth)->format('Y-m-d').' 23:59:59';

        $electricity = [
            'tarif' => 4.55,
            'start' => 0,
            'diff'  => 0,
            'end'   => 0,
            'cost_value'  => 0,
            'cost_str' => ''
        ];
        $electricitySumStartOfMonth = 0;
        $electricitySumEndOfMonth = 0;
        $electricitySumDiffOfMonth = 0;

        $water = [
            'tarif' => 27.99,
            'start' => 0,
            'diff'  => 0,
            'end'   => 0,
            'cost_value'  => 0,
            'cost_str' => ''
        ];
        $waterSumStartOfMonth = 0;
        $waterSumEndOfMonth = 0;
        $waterSumDiffOfMonth = 0;

        $heat = [
            'tarif' => 1678.72,
            'start' => 0,
            'diff'  => 0,
            'end'   => 0,
            'cost_value'  => 0,
            'cost_str'    => ''
        ];

        $waterIds = $this->get_month_water_consumptions_by_sector($sectorId, $parseStartOfMonth, $parseEndOfMonth);
        if(count($waterIds) != 0){
            for($i = 0; $i < count($waterIds); $i++){
                // dd($waterIds[$i], $parseStartOfMonth, $parseEndOfMonth);
                $tmpArrayWaterValues = $this->get_water_min_max_diff_consumptions_by_device_id($waterIds[$i]->device_id, $parseStartOfMonth, $parseEndOfMonth); 
                // dd($tmpArrayWaterValues[0]);
                $waterSumStartOfMonth += $tmpArrayWaterValues[0]->min_in_month;
                $waterSumEndOfMonth += $tmpArrayWaterValues[0]->max_in_month;
                $waterSumDiffOfMonth += $tmpArrayWaterValues[0]->diff_in_month;
            }
        }
        $water['start'] = $waterSumStartOfMonth;
        $water['diff'] = $waterSumDiffOfMonth;
        $water['end'] = $waterSumEndOfMonth;
        $water['cost_value'] = $water['diff'] * $water['tarif'];
        $water['cost_str'] = number_format($water['cost_value'], 2, ",", " ");
        // dd('start = ' . $water['start'], 'end = ' . $water['end'], 'diff = ' . $water['diff'], 'cost_value = ' . $water['cost_value'], 'cost_str = ' . $water['cost_str']);

        $electricityIds=$this->get_month_electricity_consumptions_by_sector($sectorId, $parseStartOfMonth, $parseEndOfMonth);
        if(count($electricityIds) != 0){
            for($i = 0; $i < count($waterIds); $i++){
                // dd($electricityIds[$i], $parseStartOfMonth, $parseEndOfMonth);
                $tmpArrayElectricityValues = $this->get_electricity_min_max_diff_consumptions_by_device_id($electricityIds[$i]->device_id, $parseStartOfMonth, $parseEndOfMonth); 
                // dd($tmpArrayElectricityValues[0]);
                $electricitySumStartOfMonth += $tmpArrayElectricityValues[0]->min_in_month;
                $electricitySumEndOfMonth += $tmpArrayElectricityValues[0]->max_in_month;
                $electricitySumDiffOfMonth += $tmpArrayElectricityValues[0]->diff_in_month;
            }
        }
        $electricity['start'] = $electricitySumStartOfMonth;
        $electricity['diff'] = $electricitySumDiffOfMonth;
        $electricity['end'] = $electricitySumEndOfMonth;
        $electricity['cost_value'] = $electricity['diff'] * $electricity['tarif'];
        $electricity['cost_str'] = number_format($electricity['cost_value'], 2, ",", " ");
        // dd('start = ' . $electricity['start'], 'end = ' . $electricity['end'], 'diff = ' . $electricity['diff'], 'cost_value = ' . $electricity['cost_value'], 'cost_str = ' . $electricity['cost_str']);

        $heat['end'] = $heat['start'] + $heat['diff'];
        $heat['cost_value'] = $heat['diff'] * $heat['tarif'];
        $heat['cost_str'] = number_format($heat['cost_value'], 2, ",", " ");

        $total = $heat['cost_value'] + $water['cost_value'] + $electricity['cost_value'];
        // dd('water_cost_value = ' . $water['cost_value'], 'electricity_cost_value = ' . $electricity['cost_value']);

        $total_str = number_format($total, 0, ",", " ") . " руб. " . round(100 * fmod($total, 1)) . " коп.";
        // dd('total = ' . $total, 'total_str = ' . $total_str);

        if($water['diff'] != 0){
            $water['diff'] = number_format($water['diff'], 2);
        }

        return [
            'electricity' => $electricity,
            'water'       => $water,
            'heat'        => $heat,
            'total'       => $total,
            'total_str'   => $total_str
        ];

    }

    /**
     * Вытягиваем id счётчика воды у которого есть показания за
     * {}год и {}месяц для военного городка с id={} 
     *
     * SELECT distinct(device_id) 
     * FROM water_consumptions  
     * INNER JOIN meters ON meters.id=water_consumptions.device_id 
     * where meters.building_id IN (
     * select id from buildings where sector_id=?
     * where created_at >= {startOfMonth} and created_at<= {endOfMonth}
     * )
     * 
     * @return 
     * @author Nikita Khmyrov
     */
    private function get_month_water_consumptions_by_sector($sectorId, $parseStartOfMonth, $parseEndOfMonth){
        // dd($parseStartOfMonth, $parseEndOfMonth);

        $water_meter_ids_in_sector = DB::table('water_consumptions')
        ->join('meters', 'water_consumptions.device_id', '=', 'meters.id')
        ->select('device_id')
        ->distinct()
        ->where('active', 1)
        ->whereRaw('meters.building_id IN (select id from buildings where sector_id=?)', [$sectorId])
        ->where('created_at', '>=', $parseStartOfMonth)
        ->where('created_at', '<=', $parseEndOfMonth)
        ->where('consumption_amount', '<>', 0)
        ->get();

        return $water_meter_ids_in_sector;
    }

    /**
     * Вытягиваем id счётчика воды у которого есть показания за
     * {}год и {}месяц для военного городка с id={} 
     *
     * SELECT distinct(device_id) 
     * FROM water_consumptions  
     * INNER JOIN meters ON meters.id=water_consumptions.device_id 
     * where meters.building_id IN (
     * select id from buildings where sector_id=?
     * where created_at >= {startOfMonth} and created_at<= {endOfMonth}
     * )
     * 
     * @return 
     * @author Nikita Khmyrov
     */
    private function get_month_electricity_consumptions_by_sector($sectorId, $parseStartOfMonth, $parseEndOfMonth){
        $electricity_meter_ids_in_sector = DB::table('electricity_consumptions')
        ->join('meters', 'electricity_consumptions.device_id', '=', 'meters.id')
        ->select('device_id')
        ->distinct()
        ->where('active', 1)
        ->whereRaw('meters.building_id IN (select id from buildings where sector_id=?)', [$sectorId])
        ->where('created_at', '>=', $parseStartOfMonth)
        ->where('created_at', '<=', $parseEndOfMonth)
        ->where('sumDirectActive', '<>', 0)
        ->get();

        return $electricity_meter_ids_in_sector;
    }

    /**
     * SELECT MIN(consumption_amount) as min_in_month, MAX(consumption_amount) as max_in_month, MAX(consumption_amount)-MIN(consumption_amount) as diff
     * FROM water_consumptions
     * WHERE created_at >= '{$startDateOfMonth}' and created_at <= '{$endDateOfMonth}'
     * and consumption_amount <> 0
     * AND device_id = {$deviceId}
     * 
     * 
     */
    private function get_water_min_max_diff_consumptions_by_device_id($deviceId, $startDateOfMonth, $endDateOfMonth){
            $values = DB::table('water_consumptions')
            ->select(DB::raw('MIN(consumption_amount) as min_in_month, MAX(consumption_amount) as max_in_month, 
            MAX(consumption_amount)-MIN(consumption_amount) as diff_in_month'))
            ->where('created_at', '>=', $startDateOfMonth)
            ->where('created_at', '<=', $endDateOfMonth)
            ->where('consumption_amount', '<>', 0)
            ->where('device_id', $deviceId)
            ->get();

            return $values;
    }

    private function get_electricity_min_max_diff_consumptions_by_device_id($deviceId, $startDateOfMonth, $endDateOfMonth){
        $values = DB::table('electricity_consumptions')
        ->select(DB::raw('MIN(sumDirectActive) as min_in_month, MAX(sumDirectActive) as max_in_month, 
        MAX(sumDirectActive)-MIN(sumDirectActive) as diff_in_month'))
        ->where('created_at', '>=', $startDateOfMonth)
        ->where('created_at', '<=', $endDateOfMonth)
        ->where('sumDirectActive', '<>', 0)
        ->where('device_id', $deviceId)
        ->get();

        return $values;
    }

    // private function get_random_report_object()
    // {
    //     $electricity = [
    //         'tarif' => 4.55,
    //         'start' => mt_rand(10000, 50000),
    //         'diff'  => 2000 + rand(1, 4000),
    //         'end'   => 0,
    //         'cost_value'  => 0,
    //         'cost_str' => ''
    //     ];
    //     $water = [
    //         'tarif' => 27.99,
    //         'start' => mt_rand(10000, 50000),
    //         'diff'  => 500 + rand(1, 1000),
    //         'end'   => 0,
    //         'cost_value'  => 0,
    //         'cost_str' => ''
    //     ];
    //     $heat = [
    //         'tarif' => 1678.72,
    //         'start' => mt_rand(10000, 50000),
    //         'diff'  => rand(1, 100),
    //         'end'   => 0,
    //         'cost_value'  => 0,
    //         'cost_str'    => ''
    //     ];

    //     $electricity['end'] = $electricity['start'] + $electricity['diff'];
    //     $electricity['cost_value'] = $electricity['diff'] * $electricity['tarif'];
    //     $electricity['cost_str'] = number_format($electricity['cost_value'], 2, ",", " ");

    //     $water['end'] = $water['start'] + $water['diff'];
    //     $water['cost_value'] = $water['diff'] * $water['tarif'];
    //     $water['cost_str'] = number_format($water['cost_value'], 2, ",", " ");

    //     $heat['end'] = $heat['start'] + $heat['diff'];
    //     $heat['cost_value'] = $heat['diff'] * $heat['tarif'];
    //     $heat['cost_str'] = number_format($heat['cost_value'], 2, ",", " ");

    //     $total = $heat['cost_value'] + $water['cost_value'] + $electricity['cost_value'];

    //     $total_str = number_format($total, 0, ",", " ") . " руб. " . round(100 * fmod($total, 1)) . " коп.";

    //     return [
    //         'electricity' => $electricity,
    //         'water'       => $water,
    //         'heat'        => $heat,
    //         'total'       => $total,
    //         'total_str'   => $total_str
    //     ];
    // }

}
