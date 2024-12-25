<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\ApiController;

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

Route::post('/refresh-token', [ApiAuthController::class, 'refreshToken']);


Route::middleware(['auth:api'])->group(function () {
    Route::get('/user', [ApiAuthController::class, 'user']);
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

});

