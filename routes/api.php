<?php

use App\Http\Controllers\Settings\FeaturedCourseController;
use App\Http\Controllers\Settings\HomeBannerController;
use App\Http\Controllers\Settings\PageBannerController;
use App\Http\Controllers\SystemLogs\SystemLogController;
use App\Http\Controllers\Users\UserActivityController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\AdminController;

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

Route::middleware(['log.activity', 'log.system'])->group(function () {
    Route::post('/login', [ApiAuthController::class, 'login']);
});

Route::middleware(['log.system'])->group(function () {
    Route::post('/register', [ApiAuthController::class, 'register']);
});

Route::post('/refresh-token', [ApiAuthController::class, 'refreshToken']);

Route::post('password/forgot', [PasswordResetController::class, 'forgotPassword']);
Route::post('passwords/PostReset', [PasswordResetController::class, 'PostresetPasswords']);

Route::get('/1test', function () {
    dd("test");
});

Route::middleware(['auth:api'])->group(function () {
    Route::get('/user', [ApiAuthController::class, 'user']);
    Route::get('/users/me', [ApiAuthController::class, 'user']);
    Route::put('/users', [ApiAuthController::class, 'users']);
    Route::put('/users/password', [ApiAuthController::class, 'resetPassword']);
    Route::delete('/users/me', [ApiAuthController::class, 'deleteUser']);

    Route::get('/course/{id}', [ApiController::class, 'courseDetail']);
    Route::get('/course/me', [ApiController::class, 'courseMe']);
    Route::get('/courses', [ApiController::class, 'courses']);
    Route::get('/courses/new', [ApiController::class, 'newCourses']);
    Route::get('/courses/highlight', [ApiController::class, 'highlightCourses']);
    Route::get('/courses/explore', [ApiController::class, 'exploreCourses']);
    Route::get('/courses/{id}/quiz', [ApiController::class, 'getCourseQuiz']);
    Route::get('/courses/{id}/progress', [ApiController::class, 'getCourseAction']);
    Route::get('/courses/{id}/getSuevey', [ApiController::class, 'getSurveyByCourse']);
    Route::post('/courses/{id}/review', [ApiController::class, 'PostReview']);

    Route::middleware(['log.activity'])->group(function () {
        Route::get('/courses/{id}/certificate', [ApiController::class, 'getCertificate']);
        Route::put('/courses/{id}/progress', [ApiController::class, 'upProgress']);
    });

    Route::post('/quiz/{id}/submit', [ApiController::class, 'submitQuiz']);
    Route::post('/suevey/{id}/submit', [ApiController::class, 'submitSurvey']);

    Route::middleware(['log.system'])->group(function () {
        Route::post('/logout', [ApiAuthController::class, 'logout']);
    });
});

Route::middleware(['auth:api', 'UserRole:superadmin|admin'])->group(function () {

    Route::get('/admin/course', [CourseController::class, 'index']);
    Route::get('/admin/courseReview/{id}', [CourseController::class, 'courseReview']);
    Route::get('/admin/course-review/{id}/export', [CourseController::class, 'exportCourseReview']);
    Route::get('/admin/course/{id}', [CourseController::class, 'show']);

    Route::middleware(['log.system'])->group(function () {
        Route::get('/admin/courseStatus/{id}', [CourseController::class, 'courseStatus']);
        Route::post('/admin/course', [CourseController::class, 'store']);
        Route::post('/admin/course/{id}', [CourseController::class, 'update']);
        Route::delete('/admin/course/{id}', [CourseController::class, 'destroy']);
    });

    Route::post('/admin/uploadImg', [SettingController::class, 'upPicUrl']);


    Route::get('/admin/quiz', [QuizController::class, 'index']);
    Route::get('/admin/quiz/{id}', [QuizController::class, 'show']);
    Route::get('/admin/quizQuestionList/{id}', [QuizController::class, 'quizQuestionList']);
    Route::get('/admin/quiz-questions/{id}/export', [QuizController::class, 'exportQuizQuestions']);
    Route::get('/admin/quizParticipants/{id}', [QuizController::class, 'getQuizParticipants']);
    Route::get('/admin/quiz-participants/{id}/export', [QuizController::class, 'exportQuizParticipants']);
    Route::middleware(['log.system'])->group(function () {
        Route::post('/admin/quiz', [QuizController::class, 'store']);
        Route::put('/admin/quiz/{id}', [QuizController::class, 'update']);
        Route::delete('/admin/quiz/{id}', [QuizController::class, 'destroy']);
    });

    Route::post('/admin/postQuestion/{id}', [QuizController::class, 'PostQuestion']);

    Route::get('/admin/question/{id}', [QuizController::class, 'questionID']);
    Route::post('/admin/question/{id}', [QuizController::class, 'questionUpdate']);
    Route::delete('/admin/question/{id}', [QuizController::class, 'questionDelete']);
    Route::delete('/admin/deleteAnswer/{id}', [SurveyController::class, 'deleteAnswerQuiz']);

    Route::get('/admin/getSurveyAnsList/{id}', [SurveyController::class, 'getSurveyAnsList']);
    Route::get('/admin/survey-questions/{id}/export', [SurveyController::class, 'exportSurveyQuestions']);
    Route::get('/admin/surveyParticipants/{id}', [SurveyController::class, 'getSurveyParticipants']);
    Route::get('/admin/survey-participants/{id}/export', [SurveyController::class, 'exportSurveyParticipants']);
    Route::get('/admin/survey', [SurveyController::class, 'index']);
    Route::get('/admin/survey/{id}', [SurveyController::class, 'show']);

    Route::middleware(['log.system'])->group(function () {
        Route::post('/admin/survey', [SurveyController::class, 'store']);
        Route::put('/admin/survey/{id}', [SurveyController::class, 'update']);
        Route::delete('/admin/survey/{id}', [SurveyController::class, 'destroy']);
        Route::delete('/admin/del-question-survey/{id}', [SurveyController::class, 'destroyQuestion']);
    });

    Route::post('/admin/postSurveyQuestion/{id}', [SurveyController::class, 'SurveyQuestion']);
    Route::put('/admin/survey-question/{id}', [SurveyController::class, 'updateSurveyQuestion']);
    Route::delete('/admin/answer/{id}', [SurveyController::class, 'deleteAnswer']);

    Route::get('/admin/members', [MemberController::class, 'index']);
    Route::get('/admin/member/{id}', [MemberController::class, 'getMemberDetail']);
    Route::get('/admin/export-members', [MemberController::class, 'exportMembers']);
    Route::post('/admin/members/{id}', [MemberController::class, 'update']);
    Route::delete('/admin/members/{id}', [MemberController::class, 'softDelete']);
    Route::get('/admin/userStatus/{id}', [MemberController::class, 'toggleUserStatus']);
    Route::get('/admin/member/{id}/learning-history', [MemberController::class, 'getLearningHistory']);

    Route::get('/admin/adminUser', [AdminController::class, 'index']);
    Route::post('/admin/adminUser', [AdminController::class, 'store']);
    Route::get('/admin/adminUser/{id}', [AdminController::class, 'show']);
    Route::put('/admin/adminUser/{id}', [AdminController::class, 'update']);
    Route::delete('/admin/adminUser/{id}', [AdminController::class, 'destroy']);

    Route::get('/admin/getCountry', [SettingController::class, 'getCountry']);
    Route::get('/admin/getItemForCourse ', [SettingController::class, 'getItemForCourse']);
    Route::get('/admin/getMainCategory', [SettingController::class, 'getMainCategory']);
    Route::get('/admin/getSubCategory', [SettingController::class, 'getSubCategory']);
    Route::get('/admin/getAnimalType', [SettingController::class, 'getAnimalType']);
    Route::get('/admin/getQuiz', [SettingController::class, 'getQuiz']);
    Route::get('/admin/getSurvey', [SettingController::class, 'getSurvey']);
    Route::get('/admin/getRole', [SettingController::class, 'getRole']);

    Route::get('/admin/overView', [SettingController::class, 'overView']);

    Route::get('/admin/system-logs', [SystemLogController::class, 'index']);
    Route::get('/admin/system-logs/export', [SystemLogController::class, 'exportSystemLogs']);

    Route::get('/admin/settings/page-banners', [PageBannerController::class, 'index']);
    Route::post('/admin/settings/page-banners', [PageBannerController::class, 'store']);
    Route::post('/admin/settings/page-banners/{id}/edit', [PageBannerController::class, 'update']);
    Route::delete('/admin/settings/page-banners/{id}', [PageBannerController::class, 'destroy']);

    Route::get('/admin/settings/home-banners', [HomeBannerController::class, 'index']);
    Route::post('/admin/settings/home-banners', [HomeBannerController::class, 'store']);
    Route::post('/admin/settings/home-banners/{id}/edit', [HomeBannerController::class, 'update']);
    Route::delete('/admin/settings/home-banners/{id}', [HomeBannerController::class, 'destroy']);

    Route::get('/admin/settings/featured-courses', [FeaturedCourseController::class, 'index']);
    Route::post('/admin/settings/featured-courses', [FeaturedCourseController::class, 'store']);
    Route::put('/admin/settings/featured-courses/{id}', [FeaturedCourseController::class, 'update']);
    Route::delete('/admin/settings/featured-courses/{id}', [FeaturedCourseController::class, 'destroy']);

    Route::get('/admin/settings', [\App\Http\Controllers\Settings\SettingController::class, 'index']);

    Route::get('/admin/user-activities/types', [UserActivityController::class, 'getTypes']);
    Route::get('/admin/user-activities', [UserActivityController::class, 'index']);
    Route::get('/admin/user-activities/{id}', [UserActivityController::class, 'show']);
});


