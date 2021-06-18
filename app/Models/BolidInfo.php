<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BolidInfo extends Model
{
    /**
     * Возвращает пользователей (User-ов).
     * Один человек может иметь 2 учётные записи (обычную и админку)
     * 
     * @return HasMany - коллекция объектов User-ов
     */
    public function buildings() : HasMany
    {
        return $this->hasMany('App\Models\Building', 'building_id');    
    }

    public function meters(): HasMany
    {
        return $this->hasMany('App\Models\Meter', 'meter_id'); 
    }

}