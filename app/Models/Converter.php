<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Converter extends Model
{
    public function meters() : ?HasMany
    {
        return $this->hasMany('App\Models\Meter');
    }
}
