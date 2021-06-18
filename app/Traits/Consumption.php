<?php

namespace App\Traits;

trait Consumption
{
    public function scopeAfter($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }
}