<?php

namespace App\Drivers;

use App\Abstracts\Driver;

class Impis_1 extends Driver
{
    /**
     * Канал - это адрес устройства в импульсном преобртазователе
     *
     * @var int $channel
     */
    private $channel;
    // ??
    private $serial_address;
    /**
     * Переводит число из десятичной системы исчисления
     * в шестнадцатеричную. Если число одноразрядное,
     * добавляет 0 слева
     *
     * @var callable
     */
    private $pack_int;
    private $commands;

    public function __construct($device)
    {
        parent::__construct($device);

        // $this->connection_params['protocol'] = 'tcp';

        $this->pack_int = function (integer $decimal) {
            return str_pad(dechex($decimal), 2, "0", STR_PAD_LEFT);
        };

        // Для всех имписов я пока только одно значение данного параметра
        $this->serial_address = $this->pack_int(255);
        $this->channel        = $this->pack_int($this->device->channel);

        $this->commands = [
            'write_new_channel'         => '11',
            // цена деления
            'write_device_weight'       => '13',
            'read_device_weight'        => '14',
            'write_initial_consumption' => '15',
            'get_consumption'           => '16',
        ];
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

    protected function crc_right(string $answer = ''): bool
    {
        $unpacked_answer = unpack('H*', $answer, null)[1];

        $received_crc = strtoupper(substr($unpacked_answer, -6, 4));

        $clean_answer = substr($unpacked_answer, 2, -6);
        $calculated_crc = $this->crc_mbus($clean_answer);

        return $received_crc === $calculated_crc;
    }

    // 
    /**
     * Выполняет парсинг HEX-строки с несколькими float-числоами
     * (разобраться с работой этого метода)!!!
     * @param string $str - строка в двоичном виде,
     * содержащая float число
     * @return void
     */
    private function parse_float(string $str)
    {
        // конвертируем HEX-строку в бинарную строку, а потом парсим её, полагая, что в ней находится несколько float-чисел
        $floats = unpack("f*", pack('H*', $str));

        // объявляем лямбда-функцию для использования в качестве колбэка для округления всех элементов массива
        $round_cents = function ($n) {
            return round($n, 3);
        };
        // округляем все значения до тысячных
        $floats = array_map($round_cents, $floats);

        // если обнаружено более одного числа, возвращаем их массивом, иначе одни числом
        if (count($floats) > 1) {
            return $floats;
        } else {
            return $floats[1];
        }
    }

    protected function prepare_command($str_command): string
    {
        $start_sequence = "29";
        $end_sequence   = "0D";

        // вычисляем полную длину пакета и переводим в HEX-строку
        $length = $this->packInt(7 + strlen($this->device->channel) / 2);

        // склеиваем пакет
        $command = $length . $this->serial_address . $str_command . $this->device->channel;

        // дописываем контрольную сумму
        $command .= $this->crc_mbus($command);

        $command = $start_sequence . $command . $end_sequence;

        $command_hex = pack("H*", $command);

        return $command_hex;
    }

    /**
     * Записывает показания данного счетчика
     * в свойство объекта
     *
     * @return void
     */
    private function write_consumption(): void
    {
        $consumptin_command = $this->commands['get_consumption'];

        $response = $this->make_request($consumptin_command);


        $this->consumption_record['consumption'] =
            $this->parse_float(substr($response, 10, 8));
    }

    public function collect_data()
    {
        $this->write_consumption();

        dd($this->consumption_record);
    }

    public function get_main_value()
    {
        $this->collect_data();

        return $this->consumption_record['consumption'];
    }
}