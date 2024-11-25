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

Route::get('/', function () {
    return view('welcome');
});

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Auth::routes();

Route::group(['middleware' => ['UserRole:superadmin|admin']], function() {

    Route::get('/admin/dashboard', [App\Http\Controllers\DashboardController::class, 'index']);
    Route::get('/admin/course', [App\Http\Controllers\CourseController::class, 'index']);
    Route::get('/admin/quiz', [App\Http\Controllers\QuizController::class, 'index']);
    Route::get('/admin/survey', [App\Http\Controllers\SurvayController::class, 'index']);
    Route::get('/admin/members', [App\Http\Controllers\MemberController::class, 'index']);
    Route::get('/admin/adminUser', [App\Http\Controllers\AdminController::class, 'index']);
    Route::get('/admin/setting', [App\Http\Controllers\SettingController::class, 'index']);
    Route::get('/admin/systemlogs', [App\Http\Controllers\DashboardController::class, 'systemlogs']);

});
