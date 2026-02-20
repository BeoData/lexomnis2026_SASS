<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\SystemController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/

Route::prefix('admin')->middleware(['api'])->group(function () {

    // System Monitoring
    Route::prefix('system')->group(function () {
        Route::get('health', [SystemController::class, 'health']);
        Route::get('queues', [SystemController::class, 'queues']);
        Route::get('crons', [SystemController::class, 'crons']);
        Route::get('metrics', [SystemController::class, 'metrics']);
        Route::get('activity-logs', [SystemController::class, 'activityLogs']);
    });

});
