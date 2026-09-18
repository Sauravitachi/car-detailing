<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobCardController;
Route::get('/', function () {
    return view('welcome');
});
Route::post('/api/jobcards/checkin', [JobCardController::class, 'store']);
Route::get('/track/{token}', [JobCardController::class, 'track']);