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
Route::group(['prefix' => $cpn, 'middleware' => ['auth']], function () {
    // Event route
    Route::get('events/{orderBy?}/{sortBy?}', 'EventController@show')->name('admin.Events');
    Route::get('event-reports/{orderBy?}/{sortBy?}', 'EventController@showReport')->name('admin.EventReports');
    Route::post('events/', 'EventController@store');
    Route::get('event/{id?}/{del?}', 'EventController@showDetail')->name('admin.EventDetail');
    Route::get('event-vote/{id?}', 'EventController@vote')->name('admin.EventVote');
    Route::post('event-vote/', 'EventController@voteSave');
    Route::get('event-result/{id?}', 'EventController@voteResult')->name('admin.EventResult');
    Route::post('event-result', 'EventController@voteResultAction');
    Route::get('del-vote/{aid?}/{uid?}', 'EventController@delVote');
});
