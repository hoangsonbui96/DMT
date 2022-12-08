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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Modules\ProjectManager\Http\Controllers\TaskController;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root;

try {
    $request = app()->make('Illuminate\Http\Request');
} catch (BindingResolutionException $e) {
    die("Error");
}

$cpn = is_null($request->segment(1)) ? 'akb' : $request->segment(1);
Route::prefix($cpn)->middleware(['auth'])->group(function () {
    Route::prefix('t_projects/')->group(function () {

        //Route::get('syncData','ProjectManagerController@syncMembers')->name('admin.syncMembers');

        Route::get('', 'ProjectManagerController@index')->name('admin.ProjectManager');
        Route::post('get', 'ProjectManagerController@get')->name('admin.getProjects');
        Route::get('info{id?}/{del?}', 'CommonController@showDetail')->name('admin.showProjectDetail');
        Route::get('progress/{projectId?}', 'ProjectManagerController@showProgress')->name('admin.showProgress');
        Route::post('progress-load', 'ProjectManagerController@getProgress')->name('admin.getProgress');
        Route::get('members', 'CommonController@showMembers')->name('admin.showMembers');
        Route::post('tasks', 'ProjectManagerController@getTasks')->name('admin.getProjectTasks');
        Route::post('save', 'ProjectManagerController@store')->name('admin.ProjectSave');
        Route::get('export', 'ProjectManagerController@export')->name('admin.exportProjects');
    });

    Route::get('phase-job', 'ProjectManagerController@showPhaseJob')->name('admin.showPhaseJob');

    Route::prefix('phases/')->group(function () {
        Route::get('', 'PhaseController@getById')->name('admin.getPhase');
        Route::post('save', 'PhaseController@store')->name('admin.PhaseSave');
        Route::get('show', 'PhaseController@show')->name('admin.showPhases');
    });

    Route::prefix('jobs/')->group(function () {
        Route::get('show', 'JobController@show')->name('admin.showJobs');
        Route::post('save', 'JobController@store')->name('admin.JobSave');
    });

    Route::prefix('t_tasks/')->group(function () {
        Route::get('', 'TaskController@show')->name('admin.showTasks');
        Route::post('get', 'TaskController@getTasks')->name('admin.getTasks');
        Route::get('all', 'TaskController@getAllTasks')->name('admin.getAllTasks');
        Route::post('doing', 'TaskController@getDoingTasks')->name('admin.getDoingTasks');
        Route::get('form/{projectId?}/{phaseId?}/{jobId?}', 'TaskController@showTaskForm')->name('admin.showTaskForm');
        Route::get('detail/{projectId?}/{phaseId?}/{jobId?}', 'TaskController@showTaskDetail')->name('admin.showTaskDetail');
        Route::post('save', 'TaskController@store')->name('admin.TaskSave');
        Route::post('change-status', 'TaskController@changeStatus')->name('admin.TaskChangeStatus');
        Route::get('review-report', 'TaskController@openReportTaskModal')->name('admin.openTaskReport');
        Route::post('review-report', 'TaskController@report')->name('admin.reportTask');
        Route::get('error-report', 'TaskController@openErrorTaskReport')->name('admin.openErrorTaskReport');
        Route::post('error-report', 'TaskController@reportErrorTask')->name('admin.reportErrorTask');
        Route::post('delete', 'TaskController@delete')->name('admin.TaskDelete');
        Route::post('endtime', 'TaskController@getEndTime')->name('admin.getTaskEndTime');
    });
});