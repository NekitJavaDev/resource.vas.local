<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use App\Models\Meter;
use DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // \App\Console\Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        // $filePath = "C:\Users\Shukin_A_V\Desktop\testSheduleNekit.txt";
        $schedule->call(function() {
            $waterMetersIds = DB::table('meters')
                ->select('id')
                ->where('type_id', '=', 2)
                ->where('worked', '=', 1)
                ->where('active', '=', 1)
                ->whereNotNull('server_ip')
                ->get();

            for ($i=0; $i < count($waterMetersIds); $i++) {
                $this->writeSingleMeterFreshData($waterMetersIds[$i]->id);
            }
        })->everyMinute();
    }

    /**
     * Записывает значения потребления
     * указанного типа в базу данных
     *
     * @param [int] $meter - unique ID of meter in table 'meters' 
     * @return void
     */
    public function writeSingleMeterFreshData($meter)
    {
        $meterObject = Meter::find($meter);
        try {
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
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
