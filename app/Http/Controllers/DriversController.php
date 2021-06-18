<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use DB;

class DriversController extends Controller
{
    public function show(Meter $meter)
    {
        $consumption_data = $meter
            ->connect_device()
            ->collect_data();

        return response()->json($consumption_data);
    }

    /**
     * Считывает значения потребления конкретного счётчика (общий расход и текущий) в реальном времени.
     * http://resource.vas.local/meters/{meterId}/monitoring.
     * Кнопка 'перейти к мониторингу' на вкладке потребления (с графиками)
     * 
     * @param [Meter] $meter - Объект App\Models\Meter
     * @return void
     */
    public function params(Meter $meter)
    {
        $consumption_data = $meter
            ->connect_device()
            ->write_params();

        return $consumption_data;
    }

    /**
     * Записывает значения потребления всех счётчиков
     * указанного типа в БД
     *
     * @param [type] $type - тип потребления
     * @return void
     */
    public function write($type)
    {
        Meter::active()->ofType($type)->get()
            ->each(function ($meter) {
                try {
                    $device = $meter->connect_device();

                    $consumption_data = $device->collect_data();
                    if ($consumption_data) {
                        $device->write_to_db();
                        
                        Log::info(ucfirst($meter->type->name) . ' consumption written successfully');
                    } else {
                        Log::error('The consumption wasnt collected. There must be something wrong with connection');
                    }
                } catch (\Throwable $th) {
                    Log::error('The record wasn\'t written in db. Meter: ' . $meter->id);
                }
            });

        return 'Done';
    }

    /**
     * Записывает значения потребления конкретного счётчика в таблицы БД: 
     * 'water_consumptions','electricity_consumptions' или 'heat_consumptions'
     * в зависимости от типа драйвера $device - 'App\Drivers\{DriverName}'
     *
     * @param [int] $meter - Уникальный ID (primary key) из таблицы 'meters' 
     * @return void
     */
    public function writeSingleMeterFreshData($meter)
    {
        $meterObject = Meter::find($meter);
        try{
            $device = $meterObject->connect_device();
            $consumption_data = $device->collect_data();

            if ($consumption_data) {
                $device->write_to_db();
                Log::info(ucfirst($meterObject->name) . ' consumption written successfully');
            } else {
                Log::error('The consumption wasnt collected. There must be something wrong with connection');
            }           
        } catch (\Throwable $th) {
                Log::error('The record wasn\'t written in db. Meter ID: ' . $meter);
        }
    }

    /**
     * Записывает значения потребления конкретного счётчика в таблицу БД
     * 'night_water'.
     *
     * @param [int] $meter - Уникальный ID (primary key) из таблицы 'meters' 
     * @return void
     */
    public function writeNightMonitoringWaterFreshData($meter)
    {
        $meterObject = Meter::find($meter);
        try{
            $device = $meterObject->connect_device();
            $consumption_night_data = $device->collect_night_data();

            if ($consumption_night_data) {
                if($consumption_night_data['current_consumption'] != 0){

                    DB::table('night_water')->insert([
                        'device_id' => $meter,
                        'created_at' => date('Y-m-d H:i:s'),
                        'current_consumption' => $consumption_night_data['current_consumption'],
                        'current_consumption_in_liters' => $consumption_night_data['current_consumption_in_liters'], 
                    ]);
                    
                }
                Log::info(ucfirst($meterObject->name) . ' consumption written successfully to night_water table with device_id=') . $meter;
            } else {
                Log::error('The consumption wasnt collected. There must be something wrong with connection with device_id=') . $meter;
            }           
        } catch (\Throwable $th) {
                Log::error('The record wasn\'t written in db. Meter ID: ' . $meter);
        }
    }

    /**
     * Записывает значения потребления со всех счётчиков воды, у которых счётчик импульсов - Pulsar2m. 
     * В планировщике заданий Windows в тригере ставим повторять 'каждый час' c 6.00 до 23.00, в течении 'бесконечно'.
     * Название задания - 'Pulsar2mWorkTimeOfDay'
     * 
     * @return void
     */
    public function writeCronRefreshPulsar()
    {
        $pulsarMetersIds = DB::table('meters')
            ->select('id')
            ->where('type_id', '=', 2)
            ->where('driver_id', '=', 5)
            ->where('worked', '=', 1)
            ->where('active', '=', 1)
            ->whereNotNull('server_ip')
            ->whereNotNull('rs_port')
            ->get();

        for ($i = 0; $i < count($pulsarMetersIds); $i++) {
            $this->writeSingleMeterFreshData($pulsarMetersIds[$i]->id);
        }
    }

    /**
     * Записывает значения потребления со всех счётчиков электричества - Меркурий230 и Меркурий 234.
     * В планировщике заданий Windows в тригере ставим повторять 'каждый час' c 6.00 до 23.00, в течении 'бесконечно'.
     * Название задания - 'MercuryWorkTimeOfDay'
     * 
     * @return void
     */
    public function writeCronRefreshMercury()
    {
        $mercuryMetersIds = DB::table('meters')
            ->select('id')
            ->where('type_id', '=', 1)
            ->where('driver_id', '=', 1)
            ->where('worked', '=', 1)
            ->where('active', '=', 1)
            ->whereNotNull('server_ip')
            ->whereNotNull('rs_port')
            ->get();

        for ($i = 0; $i < count($mercuryMetersIds); $i++) {
            $this->writeSingleMeterFreshData($mercuryMetersIds[$i]->id);
        }
    }

    /**
     * Записывает значения потребления со всех счётчиков воды, у которых счётчик импульсов - Pulsar2m
     * в ночное время суток (с 23:01 до 5:59) если текущее значение не 0.
     * В планировщике заданий Windows в тригере ставим повторять 'каждую минуту', в течении 'бесконечно'.
     * Название задания - 'NightPulsar2m'
     * 
     * @return void
     */
    public function writeCronRefreshPulsarNight()
    {
        $pulsarMetersIds = DB::table('meters')
            ->select('id')
            ->where('type_id', '=', 2)
            ->where('driver_id', '=', 5)
            ->where('worked', '=', 1)
            ->where('active', '=', 1)
            ->whereNotNull('server_ip')
            ->whereNotNull('rs_port')
            ->get();

        for ($i = 0; $i < count($pulsarMetersIds); $i++) {
            $this->writeNightMonitoringWaterFreshData($pulsarMetersIds[$i]->id);
        }
    }

}