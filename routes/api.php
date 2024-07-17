<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TourController;
use App\Http\Controllers\API\ContryStateCity;
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


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('getotp', [AuthController::class, 'getOtp']);
Route::post('verifyotp', [AuthController::class, 'verifyOtp']);
Route::group(['middleware' => 'jwt.verify'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('myprofile', [AuthController::class, 'myProfile']);
    Route::get('countries', [ContryStateCity::class, 'getCountries']);
    Route::get('states/{id}', [ContryStateCity::class, 'getStates']);
    Route::get('districts/{id}', [ContryStateCity::class, 'getDistricts']);
    Route::post('addtour', [TourController::class, 'addTour']);
    Route::post('deletetour/{id}', [TourController::class, 'deleteTour']);
    Route::get('gettour/{id}', [TourController::class, 'getTour']);
    Route::get('mytrip', [TourController::class, 'myTrip']);
    Route::get('tourdownload/{id}', [TourController::class, 'tourDownload']);
    Route::get('documents', [ContryStateCity::class, 'getDocuments']);
});
Route::group(['prefix'=>'admin','middleware' => ['jwt.verify','isAdmin']], function () {
    Route::get('touristscount', [TourController::class, 'touristsCount']);
    Route::get('dumpreport', [TourController::class, 'dumpReport']);
    Route::get('datewisereport', [TourController::class, 'dateWiseReport']);
    Route::get('datewisevehiclereport', [TourController::class, 'dateWiseVehicleReport']);
    Route::get('statewisetouriest', [TourController::class, 'stateWiseTouriest']);
});