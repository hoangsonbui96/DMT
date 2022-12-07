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
use Illuminate\Support\Facades\URL;

try {
    $request = app()->make('Illuminate\Http\Request');
} catch (BindingResolutionException $e) {
    die($e->getMessage());
}
$cpn = is_null($request->segment(1)) ? 'akb' : $request->segment(1);
Route::group(["prefix" => $cpn, "middleware" => "auth"], function () {
    // Documents route
    Route::get('leave', 'LeaveController@index')->name('admin.Leave');
    Route::get('leave2', 'LeaveController@index')->name('admin.Leave2');
    Route::post('absence/leave', 'LeaveController@absence')->name('admin.leaveAbsence');
    Route::post('unregistered-list/leave', 'LeaveController@getUnregisteredList')->name('admin.leave.unregistered_list');
    Route::post('notimekeeping/leave', 'LeaveController@getNoTimeKeeping')->name('admin.leave.notimekeeping_list');
    Route::post('paginateajax', 'LeaveController@paginateAjax')->name('admin.leave.paginateajax');
});