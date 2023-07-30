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

            return response()->json(['message' => 'Mine successfuly created', 'mine' => $mine], 201);
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

    public function upgradeInfrastructure($infrastructureId)
    {
        $infrastructure = Infrastructure::where('id', $infrastructureId)->first();
        $nextLevel = $infrastructure->level + 1;
        $upgradeCost = $infrastructure->construction_cost * 1.5 * $nextLevel;

        $resources = Resource::where('user_id', Auth::user()->id)->first();
        $availableOre = $resources->ore;
        $availableEnergy = $resources->energy;
        if ($infrastructure->user_id === Auth::user()->id) {
            if ($availableOre >= $upgradeCost && $availableEnergy >= 1) {
                $infrastructure->level = $nextLevel;
                $infrastructure->production_hour = (int) round($infrastructure->production_hour * 1.1);
                // $infrastructure->energy_consumption += 1;
                $infrastructure->construction_cost = $upgradeCost;
                $infrastructure->update();

                $resources->ore = $availableOre - $upgradeCost;
                $resources->energy -= 1;
                $resources->update();


                return response()->json(['message' => 'Infrastructure upgraded successfully'], 201);
            } else {
                return response()->json(['message' => 'You do not have enough resources for the upgrade. You need ' . $upgradeCost . ' ore and 1 energy.'], 401);
            }
        } else {
            return response()->json(['error' => 'You do not own this infrastructure'], 401);
        }
    }

    public function destroyInfrastructure($infrastructureId)
    {
        $infrastructure = Infrastructure::where('id', $infrastructureId)->first();
        $constructionCost = $infrastructure->construction_cost;

        if ($infrastructure->user_id === Auth::user()->id) {
            if ($infrastructure) {
                // Calculate the amount to be refunded upon destruction (50% of initial construction cost)
                $refundAmount = round($constructionCost * 0.5);

                // Increase the player's resources by the refund amount
                $resources = Resource::where('user_id', Auth::user()->id)->first();
                $resources->ore += $refundAmount;
                $resources->update();


                $infrastructure->delete();

                return response()->json(['message' => 'Infrastructure destroyed successfully. You received ' . $refundAmount . ' ore as a refund.'], 201);
            } else {
                return response()->json(['message' => 'The infrastructure is already destroyed.'], 401);
            }
        } else {
            return response()->json(['error' => 'You do not own this infrastructure'], 401);
        }
    }
}
