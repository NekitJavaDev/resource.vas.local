<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\Sector;
use App\Models\Type;
use App\Models\BolidInfo;
use Illuminate\Http\Request;
use DB;
use App\Constants\Constants;

class MetersController extends Controller
{
    public function show(Meter $meter)
    {
        return view('meters.' . $meter->type->name, compact('meter', $meter->last_consumption()));
    }

    public function showNightWater(Meter $meter)
    {
        return view('meters.water_night', compact('meter', $meter->last_night_water_consumption()));
    }

    /**
     * Вытягивает первое и последнее показание каждого дня 
     * для отрисовки графиков (диаграммы).
     *
     * @param [Meter] $meter - Объект App\Models\Meter
     * @param [int] $days - Количество последних дней, по умолчанию - 30
     * @return JSON array основных показаний электричества по двум тарифам
     * response example for meter with ID = 79 (5в/г ГЗ):
     * {
     *  14-10-2020: [{id: 33403, created_at: "2020-10-14 12:02:16", device_id: 79, consumption_amount: 830.05},…]
     *  0: {id: 33403, created_at: "2020-10-14 12:02:16", device_id: 79, consumption_amount: 830.05}
     *  consumption_amount: 830.05
     *  created_at: "2020-10-14 12:02:16"
     *  device_id: 79
     *  id: 33403
     *  1: {id: 33408, created_at: "2020-10-14 16:02:03", device_id: 79, consumption_amount: 830.19}
     *  consumption_amount: 830.19
     *  created_at: "2020-10-14 16:02:03"
     *  device_id: 79
     *  id: 33408
     *  15-10-2020: [{id: 33409, created_at: "2020-10-15 09:32:54", device_id: 79, consumption_amount: 830.39},…]
     *  0: {id: 33409, created_at: "2020-10-15 09:32:54", device_id: 79, consumption_amount: 830.39}
     *  consumption_amount: 830.39
     *  created_at: "2020-10-15 09:32:54"
     *  device_id: 79
     *  id: 33409
     *  1: {id: 33437, created_at: "2020-10-15 23:00:15", device_id: 79, consumption_amount: 830.82}
     *  consumption_amount: 830.82
     *  created_at: "2020-10-15 23:00:15"
     *  device_id: 79
     *  id: 33437
     * }
     */
    public function consumption(Meter $meter, int $days)
    {
        $consumptions = $meter->consumptions_by_days($days);

        return response()->json($consumptions);
    }

    public function consumption_by_night(Meter $meter, int $days)
    {
        $consumptions = $meter->consumptions_by_days_at_night($days);

        return response()->json($consumptions);
    }

    public function metersValues()
    {
        $active_meters = Meter::active()->get();

        $response = [];

        foreach ($active_meters as $meter) {
            $item = [];
            $item['meter_id'] = $meter->id;

            try {
                $main_value = $meter
                    ->connect_device()
                    ->get_main_value();

                $item['meter_value'] = $main_value;
            } catch (\Throwable $th) {
                $item['meter_value'] = 0;
            }

            array_push($response, $item);
        }

        return $response;
    }

    // public function waterValuesForNight()
    // {
    //     $active_meters = Meter::worked()->ofType('water')->get();

    //     $response = [];

    //     foreach ($active_meters as $meter) {
    //         $item = [];
    //         $item['meter_id'] = $meter->id;

    //         try {
    //             $main_value = $meter
    //                 ->connect_device()
    //                 ->get_main_value();

    //             $item['meter_value'] = $main_value;
    //         } catch (\Throwable $th) {
    //             $item['meter_value'] = 0;
    //         }

    //         array_push($response, $item);
    //     }

    //     return $response;
    // }

    /**
     * Вытягивает последнюю запись из таблицы electricity_consumptions
     *
     * @param [Meter] $meter - Объект App\Models\Meter
     * @return JSON array основных показаний электричества по двум тарифам
     * response example for meter with ID = 2 (ГП-1):
     * {
     *  created_at: "2020-10-05 17:26:21",
     *  device_id: 2,
     *  id: 64938,
     *  t1DirectActive: 10668.074,
     *  t1DirectReactive: 264.529,
     *  t2DirectActive: 5311.07,
     *  t2DirectReactive: 574.003
     * }
     */
    public function last_electricity_consumption(Meter $meter)
    {
        $columns = [
            'id', 'created_at', 'device_id', 't1DirectActive',
            't1DirectReactive', 't2DirectActive', 't2DirectReactive'
        ];

        $last_consumption = $meter
            ->consumptions()
            ->select($columns)
            ->latest()
            ->first();

        return response()->json($last_consumption);
    }

    /**
     * Вытягивает последнюю запись из таблицы water_consumptions
     *
     * @param Meter $meter - Объект App\Models\Meter
     * @return JSON array показаний воды по двум тарифам ('Общее потребление' и 'Текущий расход')
     * response example for meter with ID = 79 (5в/г ГЗ):
     * {
     *  consumption_amount: 830.87
     *  consumption_amount_in_liters: 830870
     *  created_at: "2020-10-16 10:00:02"
     *  current_consumption: 0.15
     *  current_consumption_in_liters: 150
     *  device_id: 79
     *  id: 33450
     * }
     */
    public function last_water_consumption(Meter $meter)
    {
        $columns = [
            'id', 'created_at', 'device_id', 'consumption_amount',
            'consumption_amount_in_liters', 'current_consumption', 'current_consumption_in_liters'
        ];

        $last_consumption = $meter
            ->consumptions()
            ->select($columns)
            ->latest()
            ->first();

        return response()->json($last_consumption);
    }

    /**
     * Вытягивает последнюю запись из таблицы 'electricity_consumptions', 'water_consumptions' или 'heat_consumptions'.
     * Таблица определяется в зависимости от значения колонки 'meters.type_id'
     *
     * @param [Meter] $meter - Объект App\Models\Meter
     * @return JSON array необходимых показаний электричества/воды/тепла по всем (4-ём) тарифам
     * response example for meter with ID = 9 (ГП-1):
     * {
     *  created_at: "2019-05-16 10:00:01",
     *  device_id: 9,
     *  id: 24945,
     *  consumption_amount: 20373.8
     * }
     */
    public function last_consumption(Meter $meter)
    {
        return response()->json($meter->last_consumption());
    }

    public function lastNightWaterConsumption(Meter $meter)
    {
        return response()->json($meter->last_night_water_consumption());
    }

    public function monitoring(Meter $meter)
    {
        return view('monitoring.' . $meter->driver->name, compact('meter'));
    }

    public function observe()
    {
        $const = new Constants;

        $sectors = Sector::all();
        $meterTypes = Type::all();
        $METER_STATUS = $const->METER_STATUS;

        return view('pages.observing', compact(['sectors', 'meterTypes', 'METER_STATUS']));
    }

    public function observe_night()
    {
        return view('pages.observing_night');
    }

    
    public function c2000_ping(Request $request, Meter $meter)
    {  
        // $meterBolidIps = DB::table('meters')
        //     ->select('server_ip')
        //     ->distinct()
        //     ->addSelect('building_id')
        //     ->distinct()
        //     ->whereNotNull('server_ip')
        //     ->whereNotNull('rs_port')
        //     ->get();

        $meterBolidIps = DB::table('meters')
            ->whereNotNull('server_ip')
            ->whereNotNull('rs_port')
            ->get();

        // $meterBolidIps = Meter::all();
                // ->select('meters.*')
                // ->leftJoin('types', 'meters.type_id', '=', 'types.id')
                // ->where('type_id', 2)
                // ->where('worked', 1)
                // ->get();

        

        dd($meterBolidIps[0]->building_id);


        for ($i = 0; $i < count($meterBolidIps); $i++) {
            // dd($this);
            $this->writeDataToBolidInfosTable($meterBolidIps[$i]);
        }
        // $meterBolidIps = ;

        // $meters = Meter::all();
                    // ->whereNotNull('server_ip')
                    // ->get();
        // $meters = DB::table('meters')
        //     ->select('server_ip')
        //     ->distinct()
        //     ->addSelect('short_name')
        //     ->join('buildings', 'meters.building_id', '=', 'buildings.id')
        //     ->whereNotNull('server_ip')
        //     ->whereNotNull('rs_port')
        //     ->groupBy('short_name')
        //     ->groupBy('server_ip')
        //     ->get();
        // dd($meters);


        $bolidIp = $meter->server_ip;

        exec("ping -n 1 $bolidIp", $output);

        // $convertedResultString = mb_convert_encoding($output[2], "UTF-8");
        // $pos = stripos($convertedResultString, 'TTL=');
        dd($output);

        // $pos = stripos($output[2], 'TTL=');
        // if($pos == null) { 
        //     return 'empty';
        // }
        // return $pos;

        // if (str_contains($convertedResultString, 'TTL=') {
        //     // dd("contains");
        //     return true;
        // }
        // if ($result == 0){
        //     return $bolidIp . " = Succes";
        // } else {
        //     return $bolidIp . " = Bad";
        // }
    }
}