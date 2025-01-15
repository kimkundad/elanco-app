<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\CourseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/register', [ApiAuthController::class, 'register']);

Route::post('/refresh-token', [ApiAuthController::class, 'refreshToken']);

Route::post('password/forgot', [PasswordResetController::class, 'forgotPassword']);
Route::post('passwords/PostReset', [PasswordResetController::class, 'PostresetPasswords']);

Route::get('/1test', function () {
    dd("test");
});

Route::middleware(['auth:api'])->group(function () {

    Route::put('/users/password', [ApiAuthController::class, 'resetPassword']);
    Route::delete('/users/me', [ApiAuthController::class, 'deleteUser']);
    Route::get('/users/me', [ApiAuthController::class, 'user']);
    Route::get('/course/me', [ApiController::class, 'courseMe']);
    Route::get('/user', [ApiAuthController::class, 'user']);
    Route::put('/users', [ApiAuthController::class, 'users']);
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/courses', [ApiController::class, 'courses']);
    Route::get('/courses/highlight', [ApiController::class, 'highlightCourses']);
    Route::get('/courses/explore', [ApiController::class, 'exploreCourses']);
    Route::get('/courses/new', [ApiController::class, 'newCourses']);
    Route::get('/course/{id}', [ApiController::class, 'courseDetail']);
    Route::get('/courses/{id}/quiz', [ApiController::class, 'getCourseQuiz']);
    Route::post('/quiz/{id}/submit', [ApiController::class, 'submitQuiz']);
    Route::get('/courses/{id}/progress', [ApiController::class, 'getCourseAction']);
    Route::put('/courses/{id}/progress', [ApiController::class, 'upProgress']);
    Route::post('/courses/{id}/review', [ApiController::class, 'PostReview']);
    Route::get('/courses/{id}/certificate', [ApiController::class, 'getCertificate']);

    Route::get('/courses/{id}/getSuevey', [ApiController::class, 'getSurveyByCourse']);
    Route::post('/suevey/{id}/submit', [ApiController::class, 'submitSurvey']);

    Route::get('/admin/course', [CourseController::class, 'index']);

});


