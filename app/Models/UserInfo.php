<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserInfo extends Model
{
    /**
     * Возвращает пользователей (User-ов).
     * Один человек может иметь 2 учётные записи (обычную и админку)
     * 
     * @return HasMany - коллекция объектов User-ов
     */
    public function sectors() : HasMany
    {
        return $this->hasMany('App\Models\User', 'role_id');    
    }

    public function fullName(): string
    {
        return $this->last_name . " " . $this->first_name . " " . $this->middle_name;
    }

    public function birthdate(): string
    {
        $birthdate = Carbon::parse($this->birthdate)->format('d.m.Y');
        return $birthdate;
    }
}