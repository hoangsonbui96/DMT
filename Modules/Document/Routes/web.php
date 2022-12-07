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
    Route::get('document/{orderBy?}/{sortBy?}', 'DocumentController@show')->name('admin.DocumentList');
    Route::post('document/', 'DocumentController@insertUpdateDoc')->name('admin.insertUpdateDoc');
    Route::get('document-detail/{id?}/{del?}', 'DocumentController@showDetail')->name('admin.DocumentDetail');
    Route::get('document-view', 'DocumentController@documentView')->name('admin.DocumentView');
    Route::get('documentDownload/{path?}', 'DocumentController@routeDownloadDoc')->name('admin.routeDownloadDoc');
    Route::post('document/check-document', 'DocumentController@checkDocument')->name('admin.document.checkDocument');
    Route::get('document-show/{id?}', 'DocumentController@showDocument')->name('admin.document.show_document')->middleware('signed');
    Route::get('get_document/{id?}', 'DocumentController@getDocument')->name('admin.document.get_document')->middleware('signed');
    Route::get('documentShow/{id}/{routeName}', 'DocumentController@getSignedUrl')->name('admin.document.signedUrl');
});

