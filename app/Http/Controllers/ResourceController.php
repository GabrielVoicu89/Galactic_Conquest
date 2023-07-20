<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    //
    public function defaultResource($userId)
    {
        $userClaimedResource = Resource::where('user_id', $userId)->first();

        if ($userClaimedResource) {
            return response()->json(['message' => 'This user already claimed free resources.'], 401);
        } else {
            $resource = new Resource();
            $resource->user_id = $userId;
            $resource->ore = 1000;
            $resource->fuel = 1000;
            $resource->energy = 20;
            $resource->save();

            return response()->json(['resource' => $resource], 200);
        }
    }
}
