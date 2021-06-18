<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Building extends Model
{
    public $timestamps = false;

    public function meters() : ?HasMany
    {
        return $this->hasMany('App\Models\Meter');
    }

    /**
     * Возвращает устройства данного здания
     * указанного типа
     *
     * @param string $type - тип устройства (water, electricity)
     * @return HasMany|null
     */
    public function special_meters(string $type) : ?HasMany
    {
        return $this->meters()->ofType($type);
    }

    /**
     * Суммирует значение счетчиков указанного типа, 
     * принадлежащих зданию
     *
     * @param string $type - тип устройства
     * @return integer $sum - потребление здания
     */
    public function consumption(string $type) : int
    {
        $sum = $this->special_meters($type)->get()->sum(function ($meter) {
            return $meter->last_consumption(null, true);
        });

        return $sum;
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo('App\Models\Sector');
    }
}
