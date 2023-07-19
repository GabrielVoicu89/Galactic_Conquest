<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\InfrastructureController;
use App\Http\Controllers\PowerPlantController;
use App\Http\Controllers\ResourceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group(['middleware' => ['web']], function () {

// });

//public routes
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/register/planet/{userId}', [AuthController::class, 'store_planet'])->name('auth.store_planet'); // acces it only once after register
Route::post('/resource/{userId}', [ResourceController::class, 'defaultResource'])->name('default_resource'); // acces it only once after creating the planet

Route::middleware('auth:sanctum')->group(function () {

    //protected routes
    Route::post('/create/mine', [InfrastructureController::class, 'buildMine'])->name('store_mine');
    Route::post('/create/refinery', [InfrastructureController::class, 'buildRefinery'])->name('store_refinery');
    Route::post('/create/powerplant', [PowerPlantController::class, 'buildPowerPlant'])->name('store_power_plant');
});
