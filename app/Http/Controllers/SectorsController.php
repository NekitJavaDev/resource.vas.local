<?php

namespace App\Http\Controllers;

use App\Models\Sector;

class SectorsController extends Controller
{
    public function show(Sector $sector)
    {
        return view('pages.sector', compact('sector'));
    }
}
