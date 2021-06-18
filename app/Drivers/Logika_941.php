<?php

namespace App\Drivers;

use App\Abstracts\Driver;
use Illuminate\Support\Facades\Log;

// !!$addressHexString="58"; - $this->device->rs_port

class Logika_941 extends Driver
{
    private $commands;

    public function __construct($device)
    {
        parent::__construct($device);

        // $this->connection_params['protocol'] = 'tcp';

        $this->commands = [
            'open_connection' => '3F00000000',
            // чтение тотальной тепловой энергии
            // 'total_energy'    => "724A03000608",

            // Чтение нескольних параметров одновременно
            // sn         t1         t2         v1         m1         q
            // 4A03004020 4A03000904 4A03000A04 4A03000008 4A03000308 4A03000608
            'read_params'     => '724A030040204A030009044A03000A044A030000084A030003084A03000608'
        ];
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

    private function calculate_value(string $data, string $type)
    {
        switch ($type) {
                // нет данных
            case '05':
                return NULL;
                break;
                // float
            case '43':
                $floatPart = unpack("f", pack('H*', $data));
                return $floatPart[1];
                break;

                // mixed int+float
            case '44':
                $intPart = hexdec(substr($data, 6, 2) . substr($data, 4, 2) . substr($data, 2, 2) . substr($data, 0, 2));
                $floatPart = unpack("f", pack('H*', substr($data, 8, 8)));
                return $intPart + $floatPart[1];
                break;

                // int unsigned
            case '41':
                return hexdec(substr($data, 6, 2) . substr($data, 4, 2) . substr($data, 2, 2) . substr($data, 0, 2));
                break;

                // ASCI строка
            case '16':
                $string = '';
                for ($i = 0; $i < strlen($data) - 1; $i += 2) {
                    $string .= chr(hexdec($data[$i] . $data[$i + 1]));
                }
                return $string;
                break;

            default:
                return NULL;
                break;
        }
    }

    private function parse_multiple($str): array
    {
        $result = [];
        // извлекаем полное полубайт полезной нагрузки
        $total_length = 2 * (hexdec(substr($str, 10, 2)) + hexdec(substr($str, 12, 2))) - 2;

        // извлекаем полезную нагрузку
        $packet_data = substr($str, 16, $total_length);
        $parse_start = 0;
        // logWrite(nice_hex_string($packet_data)." общие данные");
        // logWrite($total_length." общее число полубайт данных");
        // в цикле пока счётчик остатка не достигнет
        while ($parse_start < $total_length) {
            // logWrite($parse_start." смещение");
            $type = substr($packet_data, $parse_start, 2);
            $data_length = 2 * hexdec(substr($packet_data, $parse_start + 2, 2));
            // logWrite($data_length." длина данных в полубайтах");
            $data = substr($packet_data, $parse_start + 4, $data_length);
            // logWrite(nice_hex_string($data)." данные");
            $value = $this->calculate_value($data, $type);
            array_push($result, $value);
            $parse_start += 4 + $data_length;
        }

        return $result;
    }

    private function parse_data($hex_str)
    {
        $type = substr($hex_str, 16, 2);
        $length = substr($hex_str, 18, 2) * 2;
        $data = substr($hex_str, 20, $length);

        return $this->calculate_value($data, $type);
    }

    private function write_consumption()
    {
        // sn - тепловая энергия
        $param_names = ['sn', 't1', 't2', 'v1', 'm1', 'q'];

        $command = $this->commands['read_params'];

        $pr_command = $this->prepare_command($command);

        $hex_command = pack("H*", $pr_command);

        $response = $this->make_request($hex_command, false);

        $parsed_response = $this->parse_multiple($response);

        foreach ($param_names as $i => $param_name) {
            $this->consumption_record[$param_name] = round($parsed_response[$i], 3);
        }
        // Почему ?
        $this->consumption_record['sn'] = $this->consumption_record['sn'] - hexdec("EE000000");
    }

    public function collect_data()
    {
        if ($this->open_connection()) {
            Log::channel('meters')->info('Канал связи открыт');

            $this->write_consumption();

            return $this->consumption_record;
        } else {
            Log::channel('meters')->error('Канал связи не открыт.');
            return false;
        }
    }

    /**
     * На данный момент из параметров прибора
     * только потребление, поэтому эту функцию я оставляю,
     * чтобы не нарушать структуры
     *
     * @return void
     */
    public function write_params()
    {
        $params = $this->collect_data();

        return $params;
    }

    public function get_main_value()
    {
        $this->collect_data();

        return $this->consumption_record['v1'];
    }
}