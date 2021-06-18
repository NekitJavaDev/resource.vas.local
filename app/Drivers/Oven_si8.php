<?php

namespace App\Drivers;

use App\Abstracts\Driver;
use Illuminate\Support\Facades\Log;

/**
 * Драйвер работает. Вопрос - зачем нужен параметр totalTimer?
 * в данной версии таймер имеет вид массива, что не может являться записью в базу данных
 */
class Oven_si8 extends Driver
{
    private $commands;

    private $consumptions;

    public function __construct($device)
    {
        parent::__construct($device);

        // $this->connection_params['protocol'] = 'tcp';

        /**
         * Протокол Овен бессмысленно усложнён, расчёт всех команд - боль,
         * поэтому приводятся все возможные команды для первых 15 адресов
         */
        $this->commands = [
            '1' => ['consumption_amount' => "GHHGSHNJQOHO", 'current_consumption' => 'GHHGOVSITLVL', 'totalTimer' => 'GHHGUMPSPISM'],
            '2' => ['consumption_amount' => "GIHGSHNJUNHQ", 'current_consumption' => 'GIHGOVSIPQVN', 'totalTimer' => 'GIHGUMPSTTSK'],
            '3' => ['consumption_amount' => "GJHGSHNJTTUK", 'current_consumption' => 'GJHGOVSIQGGP', 'totalTimer' => 'GJHGUMPSUNJQ'],
            '4' => ['consumption_amount' => "GKHGSHNJNPHU", 'current_consumption' => 'GKHGOVSIGKVJ', 'totalTimer' => 'GKHGUMPSKJSG'],
            '5' => ['consumption_amount' => "GLHGSHNJKJUG", 'current_consumption' => 'GLHGOVSIJUGT', 'totalTimer' => 'GLHGUMPSNPJU'],
            '6' => ['consumption_amount' => "GMHGSHNJGSUI", 'current_consumption' => 'GMHGOVSINHGV', 'totalTimer' => 'GMHGUMPSJMJS'],
            '7' => ['consumption_amount' => "GNHGSHNJJMHS", 'current_consumption' => 'GNHGOVSIKRVH', 'totalTimer' => 'GNHGUMPSGSSI'],
            '8' => ['consumption_amount' => "GOHGSHNJSQKH", 'current_consumption' => 'GOHGOVSIRNQS', 'totalTimer' => 'GOHGUMPSVGPV'],
            '9' => ['consumption_amount' => "GPHGSHNJVGRV", 'current_consumption' => 'GPHGOVSIOTLI', 'totalTimer' => 'GPHGUMPSSQMH'],
            '10' => ['consumption_amount' => "GQHGSHNJRVRT", 'current_consumption' => 'GQHGOVSISILG', 'totalTimer' => 'GQHGUMPSOLMJ'],
            '11' => ['consumption_amount' => "GRHGSHNJOLKJ", 'current_consumption' => 'GRHGOVSIVOQU', 'totalTimer' => 'GRHGUMPSRVPT'],
            '12' => ['consumption_amount' => "GSHGSHNJIHRP", 'current_consumption' => 'GSHGOVSILSLK', 'totalTimer' => 'GSHGUMPSHRMN'],
            '13' => ['consumption_amount' => "GTHGSHNJHRKN", 'current_consumption' => 'GTHGOVSIMMQQ', 'totalTimer' => 'GTHGUMPSIHPP'],
            '14' => ['consumption_amount' => "GUHGSHNJLKKL", 'current_consumption' => 'GUHGOVSIIPQO', 'totalTimer' => 'GUHGUMPSMUPR'],
            '15' => ['consumption_amount' => "GVHGSHNJMURR", 'current_consumption' => 'GVHGOVSIGJLM', 'totalTimer' => 'GVHGUMPSLKML']
        ];

        $this->consumptions = ['consumption_amount', 'current_consumption'];
    }

    protected function get_clean_answer(string $answer): string
    {
        return substr($answer, 2, -4);
    }

    /**
     * Переопределяю метод проверки контрльной суммы,
     * потому что хуй знает, как протокол oven_si8 работает
     */
    protected function crc_right(string $answer = ''): bool
    {
        return true;
    }

    protected function prepare_command(string $consumption_type): string
    {
        $rs_port_hex = $this->device->rs_port;

        return '#' . $this->commands[$rs_port_hex][$consumption_type] . "\r";
    }

    /**
     * Переводит двоично-десятичное число из 
     * протокола Овен в нормальный вид
     *
     * @param string $ascii_str десятичное число в виде
     * бинарной строки
     * @return int $result - распаршенное десятичное число
     */
    private function extract_int(string $ascii_str): int
    {
        $result = 0;

        for ($pos = 1; $pos <= strlen($ascii_str); $pos++) {
            $digit = ord(substr($ascii_str, 0 - $pos, 1)) - 71;
            $result += $digit * pow(10, $pos - 1);
        }

        return $result;
    }

    /**
     * Извлекает время наработки устройства
     *
     * @param string $ascii_str - бинарная строка
     * @return array $time - массив времени наработки
     * устройства
     */
    private function parse_date(string $ascii_str): array
    {
        $time = [];
        $time["miliseconds"] = $this->extract_int(substr($ascii_str, 19, 2));
        $time["seconds"] = $this->extract_int(substr($ascii_str, 17, 2));
        $time["minutes"] = $this->extract_int(substr($ascii_str, 15, 2));
        $time["hours"] = $this->extract_int(substr($ascii_str, 9, 6));

        return $time;
    }

    /**
     * Парсит псевдо-float значение в виде 
     * бинарной строки в нормальный вид
     *
     * @param string $ascii_string - псевдо-float
     * значение в виде бинрной строки
     * @return integer
     */
    protected function parse_data(string $ascii_string): float
    {
        $exponent_ascii = substr($ascii_string, 9, 1);

        // полагаем, что числа всегда положительные, бит знака игнорируем
        $exponent = $this->extract_int($exponent_ascii);

        $mantissa_ascii = substr($ascii_string, 10, 7);

        $mantissa = $this->extract_int($mantissa_ascii);

        return $mantissa * pow(10, 0 - $exponent);
    }

    /**
     * Записывает значение расхода
     * водоснобжения в свойство
     * объекта
     *
     * @param string $consumption - вид потребления
     * @param callable $parser - фукнция для парсинга
     * полученных данных
     * @return void
     */
    private function write_consumption(
        string $consumption,
        callable $parser
    ): void {
        // для данного устройства ответ не парсится внутри функции make_request
        $answer = $this->make_request($consumption, true, false);

        if ($answer) {
            $this->consumption_record[$consumption] = round($parser($answer), 2);

            Log::info("Успешно получены показания: $consumption");
        } else {
            Log::error("Получение $consumption не выполнено");
        }
    }

    public function write_params()
    {
        return $this->collect_data();
    }

    /**
     * Собирает все данные о потреблении
     *
     * @return void
     */
    public function collect_data()
    {
        // Записываем общее потребление
        $this->write_consumption('consumption_amount', [$this, 'parse_data']);

        // Запсываем показание времени наработки
        // $this->write_data('totalTimer', [$this, 'parse_date']); // Don't need it yet

        return $this->consumption_record;
    }

    public function get_main_value()
    {
        $this->collect_data();

        return $this->consumption_record['consumption_amount'];
    }
}