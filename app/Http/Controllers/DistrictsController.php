<?php

namespace App\Http\Controllers;

use App\Models\MiltaryDistrict;

class DistrictsController extends Controller
{
    public function show(MiltaryDistrict $district)
    {
        if ($district && $district->objects()->exists()) {
            return view('pages.district', compact('district'));
        } else {
            return view('pages.underdevelopment');
        }
    }
}
