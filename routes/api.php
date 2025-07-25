<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WaitingListController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    Route::apiResource('waiting-list', WaitingListController::class);
});


//testing for token - testing pupose only
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('waiting-list/stats', [WaitingListController::class, 'stats']);

Route::middleware('auth:sanctum')
     ->get('waiting-list/stats/csv', [WaitingListController::class, 'exportCsv']);

