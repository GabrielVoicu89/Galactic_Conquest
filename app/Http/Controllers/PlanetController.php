<?php

namespace App\Http\Controllers;

use App\Models\Planet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanetController extends Controller
{
    public function getPositions()
    {
        $planets = Planet::all();

        return response()->json(['planets' => $planets], 200);
    }

    public function getPlanet()
    {
        $planet = Planet::where('user_id', Auth::user()->id)->get();
        return response()->json(['planet' => $planet], 200);
    }
}
