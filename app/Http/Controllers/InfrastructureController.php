<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Resource;
use Illuminate\Http\Request;

use App\Models\Infrastructure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class InfrastructureController extends Controller
{
    //
    public function buildMine(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorsFormatted = [];

            foreach ($errors as $field => $messages) {
                $errorsFormatted[$field] = $messages[0];
            }

            return response()->json(['errors' => $errorsFormatted], 400);
        }

        $resources = Resource::where('user_id', Auth::user()->id)->first();

        $availableOre = $resources->ore;
        $availableEnergy = $resources->energy;

        // dd($availableOre);

        if ($availableOre >= 300 && $availableEnergy >= 1) {
            $mine = new Infrastructure();
            $mine->user_id = Auth::user()->id;
            $mine->type = $request->type;
            $mine->level = 1;
            $mine->production_hour = 100;
            $mine->construction_cost = 300;
            $mine->finished_at = Carbon::now()->addHours(1);
            $mine->save();
            $newAvailableOre = $availableOre - $mine->construction_cost;
            $newAvailableEnergy = $availableEnergy - 1;
            // dd($newAvailableOre);

            // update resource table
            Resource::where('user_id', Auth::user()->id)->update(['ore' => $newAvailableOre, 'energy' => $newAvailableEnergy]);

            return response()->json(['message' => 'Mine successfuly created', 'mine' => $mine], 200);
        } else {
            return response()->json(['message' => 'You do not have enough resources'], 401);
        }
    }


    public function buildRefinery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorsFormatted = [];

            foreach ($errors as $field => $messages) {
                $errorsFormatted[$field] = $messages[0];
            }

            return response()->json(['errors' => $errorsFormatted], 400);
        }

        $resources = Resource::where('user_id', Auth::user()->id)->first();

        $availableOre = $resources->ore;
        $availableEnergy = $resources->energy;

        // dd($availableOre);

        if ($availableOre >= 300 && $availableEnergy >= 1) {
            $refinery = new Infrastructure();
            $refinery->user_id = Auth::user()->id;
            $refinery->type = $request->type;
            $refinery->level = 1;
            $refinery->production_hour = 100;
            $refinery->construction_cost = 300;
            $refinery->finished_at = Carbon::now()->addHours(1);
            $refinery->save();

            $newAvailableOre = $availableOre - $refinery->construction_cost;
            $newAvailableEnergy = $availableEnergy - 2;
            // dd($newAvailableOre);

            // update resource table
            Resource::where('user_id', Auth::user()->id)->update(['ore' => $newAvailableOre, 'energy' => $newAvailableEnergy]);

            return response()->json(['message' => 'Refinery successfuly created', 'refinery' => $refinery], 200);
        } else {
            return response()->json(['message' => 'You do not have enough resources'], 401);
        }
    }


    public function getRefineries()
    {
        $refineries = Infrastructure::where('user_id', Auth::user()->id)
            ->where('type', 'refinery')
            ->get();
        // dd($warehouses);
        return response()->json(['refineries' => $refineries], 200);
    }


    public function getMines()
    {
        $mines = Infrastructure::where('user_id', Auth::user()->id)
            ->where('type', 'mine')
            ->get();
        // dd($warehouses);
        return response()->json(['mines' => $mines], 200);
    }
}
