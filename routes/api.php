<?php

use Illuminate\Support\Facades\Route;
use TTBooking\WBEngine\Http\Controllers;

Route::post('/search', Controllers\SearchController::class)->name('search');
Route::get('/search/{session}', [Controllers\SearchController::class, 'load'])->name('loadSearch');
Route::post('/select/{session}', Controllers\SelectController::class)->name('select');
