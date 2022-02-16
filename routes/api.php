<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/rest-api', function () {
//     return Employee::all();
// });

Route::get('/rest-api', [EmployeeController::class, 'api']);
// Route::get('/rest-api', [EmployeeController::class, 'show']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

