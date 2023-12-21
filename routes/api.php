<?php

use Illuminate\Support\Facades\Route;
use TTBooking\WBEngine\Http\Controllers;

Route::post('/search', Controllers\SearchController::class)->name('search');
Route::post('/select/{session}', Controllers\SelectController::class)->name('select');
