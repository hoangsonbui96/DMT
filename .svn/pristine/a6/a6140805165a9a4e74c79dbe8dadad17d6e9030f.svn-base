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
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root;

try {
    $request = app()->make('Illuminate\Http\Request');
} catch (BindingResolutionException $e) {
    die("Error");
}

$cpn = is_null($request->segment(1)) ? 'akb' : $request->segment(1);
Route::prefix($cpn)->middleware(['auth'])->group(function () {
    // interviewjob
    Route::prefix('t_interviewJob/')->group(function () {
        Route::get('', 'InterviewJobController@interviewJobList')->name('admin.interviewJob.list');
        Route::get('addJob', 'InterviewJobController@interviewJobAdd')->name('admin.interviewJob.add');
        Route::post('saveJob', 'InterviewJobController@interviewJobStore')->name('admin.interviewJob.store');
        Route::get('editJob', 'InterviewJobController@interviewJobEdit')->name('admin.interviewJob.edit');
        Route::post('updateJob', 'InterviewJobController@interviewJobUpdate')->name('admin.interviewJob.update');
        Route::post('deleteJob', 'InterviewJobController@interviewJobDelete')->name('admin.interviewJob.delete');
        Route::post('changeActiveJob', 'InterviewJobController@interviewJobChangeActive')->name('admin.interviewJob.changeActive');
    });

    // candidateSchedule
    Route::prefix('t_candidates/')->group(function () {
        Route::get('list/{jobId?}', 'CandidatesController@candidateList')->name('admin.candidates.list');
        Route::post('addCandidate', 'CandidatesController@candidateAdd')->name('admin.candidates.add');
        Route::post('saveCandidate', 'CandidatesController@candidateStore')->name('admin.candidates.store');
        Route::post('editCandidate', 'CandidatesController@candidateEdit')->name('admin.candidates.edit');
        Route::post('updateCandidate', 'CandidatesController@candidateUpdate')->name('admin.candidates.update');
        Route::post('deleteCandidate', 'CandidatesController@candidateDelete')->name('admin.candidates.delete');
        Route::post('download', 'CandidatesController@candidateDownload')->name('admin.candidates.download_cv');
        Route::get('get_cv/{file?}', 'CandidatesController@candidateGetCV')->name('admin.candidates.get_cv');
        Route::post('interviewSchedule', 'CandidatesController@interviewSheduleAdd')->name('admin.interviewShedule.add');
        Route::post('saveInterviewSchedule', 'CandidatesController@interviewSheduleStore')->name('admin.interviewShedule.store');
        Route::post('editInterviewSchedule', 'CandidatesController@interviewSheduleEdit')->name('admin.interviewShedule.edit');
        Route::post('updateInterviewSchedule', 'CandidatesController@interviewSheduleUpdate')->name('admin.interviewShedule.update');
        Route::post('check_cv', 'CandidatesController@candidateCheckCV')->name('admin.candidates.check_cv');
        Route::get('list/{jobId?}/{candidateId?}', 'CandidatesController@candidateShowCV')->name('admin.candidates.show_cv');
        Route::post('decide_cv', 'CandidatesController@candidateDecideCV')->name('admin.candidates.decide_cv');
    });
});
