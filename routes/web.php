<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Auth::routes();

Route::group(['middleware' => ['UserRole:superadmin|admin']], function() {

    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/admin/dashboard', [App\Http\Controllers\DashboardController::class, 'index']);
    Route::resource('/admin/course', App\Http\Controllers\CourseController::class);
    Route::resource('/admin/quiz', App\Http\Controllers\QuizController::class);
    Route::post('/admin/PostQuestion/{id}', [App\Http\Controllers\QuizController::class, 'PostQuestion']);
    Route::post('/admin/course/toggle-status/{id}', [App\Http\Controllers\CourseController::class, 'toggleStatus']);
    Route::get('/admin/course/{id}/details', [App\Http\Controllers\CourseController::class, 'getDetails']);

    Route::get('/admin/survey', [App\Http\Controllers\SurvayController::class, 'index']);
    Route::resource('/admin/members', App\Http\Controllers\MemberController::class);
    Route::resource('/admin/adminUser', App\Http\Controllers\AdminController::class);
    Route::get('/admin/setting', [App\Http\Controllers\SettingController::class, 'index']);
    Route::get('/admin/systemlogs', [App\Http\Controllers\DashboardController::class, 'systemlogs']);

});
