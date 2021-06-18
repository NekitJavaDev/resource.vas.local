<?php

namespace App\Http\Controllers;

use App\Models\MiltaryObject;

class ObjectsController extends Controller
{
    public function show(MiltaryObject $object)
    {
        return view('pages.object', compact('object'));
    }
}
