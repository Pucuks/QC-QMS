<?php

use App\Http\Controllers\HomeController;
use Encore\Admin\Grid\Filter\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('chart')->controller(HomeController::class)->group(function (): void {
    Route::get('document', 'echart');
});

// Route::prefix('chart')->group(function () {
//     Route::get('', [HomeController::class, 'echart']);
// });