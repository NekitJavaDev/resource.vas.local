<?php

namespace App\Drivers;

use App\Abstracts\Driver;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DB;

class Pulsar_2m extends Driver
{
    private $channels_masks;
    private $commands;
    private $mask;
    private $consumptions;

    public function __construct($device)
    {
        parent::__construct($device);

        $this->channels_masks = [
            '1'     => "01000000",
            '2'     => "02000000",
            // чтение всех каналов единовременно
            '-1'    => "03000000"
        ];

        $this->commands = [
            // чтение значения потреблений
            'consumption_amount'  => '01',
            // чтение значения среднего потребления
            'current_consumption'  => '3E',
            // чтение значения времени наработки
            'total_timer_command' => '04'
        ];

        $this->consumptions = ['consumption_amount', 'current_consumption', 'consumption_amount_in_liters', 'current_consumption_in_liters'];
    }

    protected function crc_mbus(string $msg): string
    {
        $data = pack('H*', $msg);
        $crc = 0xFFFF;

        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= ord($data[$i]);

            for ($j = 8; $j != 0; $j--) {
                if (($crc & 0x0001) != 0) {
                    $crc >>= 1;
                    $crc ^= 0xA001;
                } else $crc >>= 1;
            }
        }
        $crc = sprintf('%04X', $crc);
        // меняем порядок байтов, как требует протокол
        $crc_inverted = substr($crc, 2, 2) . substr($crc, 0, 2);
        return $crc_inverted;
    }

    protected function get_clean_answer($answer): string
    {
        return substr($answer, 0, -4);
    }

    // address - rs_port hex
    // command - 01 | 3E | 04
    // data - mask - 01000000 || 02000000 || 03000000
    protected function prepare_command(string $command_name): string
    {
        $command = $this->commands[$command_name];

        $rs_port_hex = '00' . $this->device->rs_port;

        $length = 10 + strlen($this->mask) / 2;
        Log::info("data :" . $this->mask);
        Log::info("Length :" . $length);

        $length = strtoupper(dechex($length));
        $length = str_pad($length, 2, "0", STR_PAD_LEFT);
        $id = "1234";
        $commandString = $rs_port_hex . $command . $length . $this->mask . $id;
        $commandString .= $this->crc_mbus($commandString);

        Log::info("commandString :" . $commandString);

        return pack("H*", $commandString);
    }

    private function parse_date($string)
    {
        return unpack("Cyear/Cmonth/Cday/Chour/Cminutes/Cseconds", pack('H*', $string));
    }

    // выполняет парсинг HEX-строки с несколькими double-числами
    private function parse_data(string $string)
    {
        // конвертируем HEX-строку в бинарную строку, парсим её, полагая, что в ней находятся double-числа
        $floats = unpack("d*", pack('H*', $string));
        // объявляем лямбда-функцию для использования в качестве колбэка
        $roundCents = function ($n) {
            return round($n, 2);
        };

        // округляем все числа массива до 2 знаков используя лямбду выше
        $floats = array_map($roundCents, $floats);
        // если обнаружено более одного числа, возвращаем их массивом, иначе одним числом
        if (count($floats) > 1) {
            return $floats;
        } else {
            return $floats[1];
        }
    }

    private function write_data(
        string $consumption,
        callable $parser
    ): void {
        $answer = $this->make_request($consumption);

        if ($answer) {
            $this->consumption_record[$consumption] = $parser(substr($answer, 12, -8));

            Log::info("Успешно получены показания: $consumption");
        } else {
            Log::error("Получение $consumption не выполнено");
        }
    }

    public function collect_data()
    {
        $device_type_command = '00' . $this->device->rs_port . "0302460001";
        $device_type_command .= $this->crc_mbus($device_type_command);

        $device_type_command = pack("H*", $device_type_command);

        $response = $this->make_request($device_type_command, false);

        //снимаем текущее показания Некит
        // $prepare_command_pack_value = $this->prepare_command('current_consumption');
        // $consumptin_command = $this->commands['current_consumption'];
        // $responseCurrentValueFromMeter = $this->make_request($prepare_command_pack_value, true,true, true);//comand №2
        // var_dump($responseCurrentValueFromMeter);

        if ($response) {
            $type_id = strtoupper(substr($response, 8, 8));
            if ($type_id === "03029A00") {
                $this->mask = $this->channels_masks[$this->device->channel()];

                // Записываем общее потребление
                $this->write_data('consumption_amount', [$this, 'parse_data']);
                $this->write_data('current_consumption', [$this, 'parse_data']);

                $this->consumption_record['current_consumption_in_liters'] = $this->consumption_record['current_consumption'] * 1000;
                $this->consumption_record['consumption_amount_in_liters'] = $this->consumption_record['consumption_amount'] * 1000;
                // $this->write_data('total_timer_command', [$this, 'parse_data']);
                
                // dd($this);

                /**
                 * Запсываем показание времени наработки
                 * Эта команда отсутствует в предыдущем переборе команд, 
                 * потому что нужен другой парсер для даты
                 */
                // $totalTimerCommand = '04';

                /**
                 * Для всех команд кроме этой используется маска
                 * Поэтому обнуляем значение mask
                 */
                // $this->mask = '';
                // $this->write_data('total_timer_comand', [$this, 'parse_date']);
                return $this->consumption_record;
            }
        }
    }

    public function write_params()
    {
        return $this->collect_data();    
    }

    public function get_main_value()
    {
        $this->collect_data();

        return $this->consumption_record['consumption_amount'];
    }

    public function collect_night_data()
    {
        $device_type_command = '00' . $this->device->rs_port . "0302460001";
        $device_type_command .= $this->crc_mbus($device_type_command);
        $device_type_command = pack("H*", $device_type_command);

        $response = $this->make_request($device_type_command, false);

        if ($response) {
            $type_id = strtoupper(substr($response, 8, 8));
            if ($type_id === "03029A00") {
                $this->mask = $this->channels_masks[$this->device->channel()];

                $this->write_data('current_consumption', [$this, 'parse_data']);
                $this->consumption_record['current_consumption_in_liters'] = $this->consumption_record['current_consumption'] * 1000;
                
                return $this->consumption_record;
            }
        }
    }

    // public function write_water_consumptions_to_db($compution_amount_float_value, $device_id)
    // {
    //     // Запись запрашиваемых значений в таблицу water_consumptions
    //             $nowDate = Carbon::now();
    //             $parseDate = Carbon::parse($nowDate)->format('Y-m-d H:m:s');
    //             dd($compution_amount_float_value);
    //             $lastRecordInWaterConsumptions = DB::table('water_consumptions')
    //                                                 ->get();
    //             DB::insert(
    //                 'insert into water_consumptions (device_id, created_at, consumption_amount) values (?,?,?)',
    //                 [$device_id, $parseDate, $compution_amount_float_value]);
    //             // $tmp_consumption_amount = $this->consumption_record['consumption_amount'];
    //             // return response()->json($tmp_consumption_amount,$parse_date);
    //             dd('insert was!');
    // }
    
}