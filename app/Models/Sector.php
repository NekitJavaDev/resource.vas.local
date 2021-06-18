<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    /**
     * Здания, принадлежащие военному городку
     *
     * @return HasMany - коллекция зданий
     */
    public function buildings() : HasMany
    {
        return $this->hasMany('App\Models\Building', 'sector_id');
    }

    /**
     * Суммирует количество счетчиков городка
     *
     * @param string $type - тип счетчиков
     * @return integer $count - кол-во приборов учета
     */
    public function meters_count(string $type = null) : int
    {
        $count = $this->buildings->sum(function ($building) use ($type) {
            return $type ? 
                $building->special_meters($type)->count() : 
                $building->meters->count();
        });

        return $count;
    }

    /**
     * Суммирует нынешнее потребление ресурсов
     * по городку
     *
     * @param string $type - тип потребления
     * @return integer $sum - сумма расхода
     */
    public function consumption(string $type) : int
    {
        $sum = $this->buildings
            ->sum(function ($building) use ($type) {
                return $building->consumption($type);
        });

        return $sum;
    }

    public function object(){
        return $this->belongsTo('App\Models\MiltaryObject');
    }
}
