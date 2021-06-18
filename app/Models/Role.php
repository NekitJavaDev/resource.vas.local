<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /**
     * Возвращает пользователей,
     * принадлежащих данному типу (админы или просто пользователи)
     * 
     * @return HasMany - коллекция объектов User-ов
     */
    public function sectors() : HasMany
    {
        return $this->hasMany('App\Models\User', 'role_id');    
    }

}