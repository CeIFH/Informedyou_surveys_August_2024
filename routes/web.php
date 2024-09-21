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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;
use Wave\Facades\Wave;

use App\Http\Livewire\Wave\SurveyBuilder;
use App\Http\Livewire\Wave\SurveyResponse;
use App\Http\Livewire\Wave\Home;
use App\Http\Livewire\Wave\FolderShow;
use App\Http\Livewire\Wave\CompletionMessage;
use App\Http\Livewire\Wave\SurveyExport;
use App\Http\Livewire\Wave\SurveyDelete;
use App\Http\Livewire\Wave\SurveyDuplicate;
use App\Http\Livewire\Wave\SurveyShow;
use App\Http\Livewire\Wave\Dashboard;

//dashboards
Route::get('/survey/dashboard', Home::class)->name('home');
Route::get('/dashboards', Dashboard::class)->name('dashboard');

// Surveys

Route::get('/survey/create', SurveyBuilder::class)->name('survey.create');
Route::get('/survey/{id}', SurveyResponse::class)->name('survey.show');
Route::get('/survey/{survey}/response/{response}/completion', CompletionMessage::class)->name('completion.message');
Route::get('/survey/{surveyId}/edit', SurveyBuilder::class)->name('survey.edit');
Route::get('/survey/duplicate/{id}', SurveyDuplicate::class)->name('survey.duplicate');
Route::get('/survey/{id}/export', SurveyExport::class)->name('survey.export');
Route::delete('/survey/{id}/delete', SurveyDelete::class)->name('survey.delete');
/* Route::get('/survey/{surveyId}', SurveyShow::class)->name('survey.show'); */

// Folders
Route::get('/folder/{id}', FolderShow::class)->name('folder.show');


// Completion message route
Route::get('/survey/{survey}/completion/{response}', CompletionMessage::class)->name('survey.completion');


// Authentication routes
Auth::routes();

// Voyager Admin routes
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

// Wave routes
Wave::routes();
