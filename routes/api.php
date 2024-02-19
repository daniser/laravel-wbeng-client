<?php

use Illuminate\Support\Facades\Route;
use TTBooking\WBEngine\Http\Controllers;

Route::get('/airports/{input}', [Controllers\AutocompleteController::class, 'airports'])->name('airports');
Route::get('/airlines/{input}', [Controllers\AutocompleteController::class, 'airlines'])->name('airlines');
Route::post('/search', Controllers\SearchController::class)->name('search');
Route::get('/search/{session}', [Controllers\SearchController::class, 'load'])->name('loadSearch');
Route::post('/select/{session}', Controllers\SelectController::class)->name('select');
Route::get('/select/{session}', [Controllers\SelectController::class, 'load'])->name('loadSelect');
