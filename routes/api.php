<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SurvayController;
use App\Http\Controllers\MemberController;

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
    Route::get('/logout', [ApiAuthController::class, 'logout']);
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

});



Route::middleware(['auth:api', 'UserRole:superadmin|admin'])->group(function () {

    Route::get('/admin/course', [CourseController::class, 'index']);
    Route::post('/admin/course', [CourseController::class, 'store']);
    Route::post('/admin/course/{id}', [CourseController::class, 'update']);
    Route::get('/admin/course/{id}', [CourseController::class, 'show']);
    Route::delete('/admin/course/{id}', [CourseController::class, 'destroy']);

    Route::post('/admin/uploadImg', [SettingController::class, 'upPicUrl']);

    Route::get('/admin/quiz', [QuizController::class, 'index']);
    Route::get('/admin/quiz/{id}', [QuizController::class, 'show']);
    Route::post('/admin/quiz', [QuizController::class, 'store']);
    Route::put('/admin/quiz/{id}', [QuizController::class, 'update']);
    Route::delete('/admin/quiz/{id}', [QuizController::class, 'destroy']);

    Route::post('/admin/postQuestion/{id}', [QuizController::class, 'PostQuestion']);
    Route::get('/admin/question/{id}', [QuizController::class, 'questionID']);
    Route::post('/admin/question/{id}', [QuizController::class, 'questionUpdate']);

    Route::get('/admin/survey', [SurvayController::class, 'index']);
    Route::post('/admin/survey', [SurvayController::class, 'store']);
    Route::get('/admin/survey/{id}', [SurvayController::class, 'show']);
    Route::put('/admin/survey/{id}', [SurvayController::class, 'update']);
    Route::delete('/admin/survey/{id}', [SurvayController::class, 'destroy']);

    Route::post('/admin/postSurveyQuestion/{id}', [SurvayController::class, 'SurveyQuestion']);
    Route::put('/admin/survey-question/{id}', [SurvayController::class, 'updateSurveyQuestion']);
    Route::delete('/admin/answer/{id}', [SurvayController::class, 'deleteAnswer']);

    Route::get('/admin/members', [MemberController::class, 'index']);
    Route::get('/admin/export-members', [MemberController::class, 'exportMembers']);
    Route::post('/admin/members/{id}', [MemberController::class, 'update']);

    Route::get('/admin/getCountry', [SettingController::class, 'getCountry']);
    Route::get('/admin/getItemForCourse ', [SettingController::class, 'getItemForCourse']);
    Route::get('/admin/getMainCategory', [SettingController::class, 'getMainCategory']);
    Route::get('/admin/getSubCategory', [SettingController::class, 'getSubCategory']);
    Route::get('/admin/getAnimalType', [SettingController::class, 'getAnimalType']);
    Route::get('/admin/getQuiz', [SettingController::class, 'getQuiz']);
    Route::get('/admin/getSurvey', [SettingController::class, 'getSurvey']);

});


