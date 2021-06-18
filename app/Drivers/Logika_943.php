<?php

namespace App\Drivers;

use App\Abstracts\Driver;
use Illuminate\Support\Facades\Log;

// !!$addressHexString="58"; - $this->device->rs_port
// 58
class Logika_943 extends Driver
{
    /**
     * Команды для получения значений
     * потреблений/параметров
     *
     * @var array
     */
    private $commands;
    /**
     * Данные счетчика, вносимые в базу
     *
     * @var array
     */
    private $consumption_params;

    public function __construct($device)
    {
        parent::__construct($device);

        $this->commands = [
            'open_connection' => '3F00000000',
            // чтение тотальной тепловой энергии
            't1' => '5218020400',
            't2' => '521C020400',
            'g1' => '5204020400',
            'g2' => '5208020400',
            'p1' => '5210020400',
            'p2' => '5214020400',
            // чтение приращения параметров
            'm1_delta' => '522C050400',
            'm2_delta' => '523C050400',
            'v1_delta' => '5220050400',
            'v2_delta' => '5224050400',
            'q_delta' => '5238050400',
            // чтение q FLASH / чтение fixed параметров
            'fixed_data' => '4509010200'
        ];

        $this->consumption_params = ['v1', 'v2', 'm1', 'm2', 'q'];
    }

    protected function get_clean_answer($answer): string
    {
        return substr($answer, 2, -4);
    }

    protected function crc_mbus(string $msg): string
    {
        $buffer = pack('H*', $msg);

        $result = 0x0000;

        if (($length = strlen($buffer)) > 0) {
            for ($offset = 0; $offset < $length; $offset++) {
                $result ^= (ord($buffer[$offset]) << 8);
                for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                    if (($result <<= 1) & 0x10000) $result ^= 0x1021;
                    $result &= 0xFFFF;
                }
            }
        }

        $result = sprintf('%04X', $result);
        return $result;
    }

    protected function prepare_command($str_command): string
    {
        $command_len = strtoupper(dechex(strlen($str_command) / 2));

        $command_len = str_pad($command_len, 4, "0", STR_PAD_LEFT);

        $command_len = substr($command_len, 2, 2) . substr($command_len, 0, 2);

        $str_command = $this->device->rs_port . "900000" . $command_len . $str_command;

        $MAGIC_NUMBER = 10;

        $str_command .= $this->crc_mbus($str_command);

        return $MAGIC_NUMBER . $str_command;
    }

    private function open_connection(): bool
    {
        $prefix = 'ffffffffffffffffffffffffffffffffffffff';

        $command = $this->commands['open_connection'];

        $pr_command = $this->prepare_command($command);

        $pr_command = $prefix . $pr_command;

        $hex_command = pack("H*", $pr_command);
        // !!здесь я убираю подготовку команды, т.к. уже самостоятельно
        // воспользовался командой prepare_command и добавил приставку
        $response = $this->make_request($hex_command, false, true);

        return boolval($response);
    }

    /**
     * Переводит закодированное в HEX-строку float 
     * число стандарта PIC в десятичный вид
     *
     * @param string $hex_string
     * @return float 
     */
    private function float_pic_decode(string $hex_string): float
    {
        $hex_stringReversed = substr($hex_string, 6, 2) . substr($hex_string, 4, 2) . substr($hex_string, 2, 2) . substr($hex_string, 0, 2);
        $number = hexdec($hex_stringReversed);
        $order = $number >> 24 & 0x000000FF;
        $sign = $number >> 23 & 0x00000001;
        $mantissaBits = $number & 0x007FFFFF | 0x00800000;
        $mantissa = 0;
        for ($position = 0; $position < 24; $position++) {
            $bit = 0x1 & $mantissaBits >> 23 - $position;
            $mantissa += $bit * pow(2, -$position);
        }
        $float = pow(-1, $sign) * $mantissa * pow(2, $order - 127);

        return $float;
    }

    /**
     * Выделяет из дампа ОЗУ запрашиваемое значение
     *
     * @param string $hex_string
     * @return float
     */
    private function parse_raw_data(string $hex_string): float
    {
        $data = substr($hex_string, 16, 8);

        return $this->float_pic_decode($data);
    }

    /**
     * Переводит закодированное в HEX-строку 
     * integer число в десятичный вид
     *
     * @param string $hex_string
     * @return int
     */
    private function int_decode(string $hex_string): int
    {
        $hex_string_reversed = substr($hex_string, 6, 2) . substr($hex_string, 4, 2) . substr($hex_string, 2, 2) . substr($hex_string, 0, 2);

        $number = hexdec($hex_string_reversed);

        return $number;
    }

    /**
     * Собирает информацию о fixed(??) параметрах
     * теплового счетчика
     *
     * @return array -  fixed параметры устройства
     */
    private function get_fixed_data(): array
    {
        $fixed_data_command = $this->commands['fixed_data'];

        $respone_str = $this->make_request($fixed_data_command);

        $response_arr = str_split(substr($respone_str, 36), 16);

        $fixed_data = [];

        // в ответе также содержится параметр "time", который является
        // последним элементом массива $response_arr, однако я его 
        // упустил здесь из-за отсутствия необходимости
        foreach ($this->consumption_params as $i => $param) {
            $fixed_param = $response_arr[$i];

            $fixed_data[$param] = $this->int_decode(substr($fixed_param, 0, 8)) +
                $this->float_pic_decode(substr($fixed_param, 8));
        }

        return $fixed_data;
    }

    /**
     * Собирает информацию о делта(??) параметрах
     * теплового счетчика
     *
     * @return array - делта параметры устройства
     */
    private function get_delta_data(): array
    {
        $delta_data = array_map(function ($param) {
            $delta_command = $this->commands[$param . "__delta"];

            $delta_response = $this->make_request($delta_command);

            return $this->parse_raw_data($delta_response);
        }, $this->consumption_params);

        return $delta_data;
    }

    private function write_consumption()
    {
        $fixed_data = $this->get_fixed_data();

        $delta_data = $this->get_delta_data();

        $this->consumption_record = array_map(function ($param)
        use ($fixed_data, $delta_data) {
            return $fixed_data[$param] + $delta_data[$param];
        }, $this->consumption_params);
    }

    public function collect_data()
    {
        if ($this->open_connection()) {
            Log::info('Канал связи открыт');

            $this->write_consumption();

            return $this->consumption_record;
        } else {
            Log::error('Канал связи не открыт.');
            return false;
        }
    }

    public function get_main_value()
    {
        $this->collect_data();

        return $this->consumption_record['v1'] + $this->consumption_record['v2'];
    }
}