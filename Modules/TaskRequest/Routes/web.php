<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Route;

try {
    $request = app()->make('Illuminate\Http\Request');
} catch (BindingResolutionException $e) {
    die("Error");
}

$cpn = is_null($request->segment(1)) ? 'akb' : $request->segment(1);
Route::prefix($cpn)->middleware(['auth'])->group(function () {
    //Request a Task
    Route::get('request_task', 'TaskRequestController@show')->name('admin.TaskRequest');
    Route::get('request_tasks_detail/{id?}/{del?}', 'TaskRequestController@showDetail')->name('admin.TaskRequestDetail');
    Route::post('request_tasks_save', 'TaskRequestController@store')->name('admin.Insert');
    Route::get('request_tasks_review/{id?}/{del?}', 'TaskRequestController@review')->name('admin.TaskRequestReview');
});
