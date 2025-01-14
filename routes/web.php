<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\HomeController;
use App\Models\Quiz;

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

Route::get('/getPdf', [HomeController::class, 'generateCertificate']);


    Route::get('/email/verify', [ApiController::class, 'verifyEmail'])->name('verification.verify');


    Route::get('/test-email', function () {

        $id = 4;
        $quiz = Quiz::with('questions.answers')->findOrFail($id);
        dd(count($quiz->questions));

    });

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

    Route::get('/admin/user/{userId}/courses', [App\Http\Controllers\MemberController::class, 'getUserCourses']);

    Route::get('/admin/survey', [App\Http\Controllers\SurvayController::class, 'index']);
    Route::resource('/admin/members', App\Http\Controllers\MemberController::class);
    Route::resource('/admin/adminUser', App\Http\Controllers\AdminController::class);
    Route::get('/admin/setting', [App\Http\Controllers\SettingController::class, 'index']);
    Route::get('/admin/systemlogs', [App\Http\Controllers\DashboardController::class, 'systemlogs']);

});
