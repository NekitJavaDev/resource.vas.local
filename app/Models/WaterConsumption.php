<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Consumption;

class WaterConsumption extends Model
{
    use Consumption;

    const UPDATED_AT = null;

    protected $guarded = [];
}
