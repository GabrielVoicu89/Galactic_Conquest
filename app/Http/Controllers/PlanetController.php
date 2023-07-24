<?php

namespace App\Http\Controllers;

use App\Models\Planet;
use Illuminate\Http\Request;

class PlanetController extends Controller
{
    public function getPositions()
    {
        $planets = Planet::all();

        return response()->json(['planets' => $planets], 200);
    }
}
