<?php

use App\Http\Controllers\JobCardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/checkin/{workshop}/create', [JobCardController::class, 'create'])->name('checkin.create');
Route::post('/api/workshops/{workshop}/jobcards', [JobCardController::class, 'store'])->name('jobcards.store');
Route::get('/track/{token}', [JobCardController::class, 'track'])->name('track');
