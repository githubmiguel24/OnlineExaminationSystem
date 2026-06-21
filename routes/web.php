<?php

use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\StudentExamController;
use Illuminate\Support\Facades\Route;


// Student auth routes

Route::group(['prefix' => 'student-auth'], function () {

    Route::get('/register', [StudentAuthController::class, 'showRegister'])->name('studentAuth.register');
    Route::post('/register', [StudentAuthController::class, 'register'])->name('studentAuth.register.submit');
    Route::get('/login', [StudentAuthController::class, 'showLogin'])->name('studentAuth.login');
    Route::post('/login', [StudentAuthController::class, 'login'])->name('studentAuth.login.submit');
    Route::get('/forgot-password', [StudentAuthController::class, 'showForgotPassword'])->name('studentAuth.forgotPassword');
    Route::post('/forgot-password', [StudentAuthController::class, 'forgotPassword'])->name('studentAuth.forgotPassword.submit');

    Route::group(['middleware' => 'check.student.auth'], function () {
        Route::get('/dashboard', [StudentAuthController::class, 'dashboard'])->name('studentAuth.dashboard');
        Route::post('/logout', [StudentAuthController::class, 'logout'])->name('studentAuth.logout');
    });
});

// Student exam routes (login required)
Route::group(['prefix' => 'student-exam', 'middleware' => 'check.student.auth'], function () {

    Route::get('/', [StudentExamController::class, 'examList'])->name('studentExam.list');
    Route::get('/{examId}/instructions', [StudentExamController::class, 'examInstructions'])->where('examId', '[0-9]+')->name('studentExam.instructions');
    Route::get('/{examId}/start', [StudentExamController::class, 'startExam'])->where('examId', '[0-9]+')->name('studentExam.start');
    Route::post('/{examId}/submit', [StudentExamController::class, 'submitExam'])->where('examId', '[0-9]+')->name('studentExam.submit');
    Route::get('/{examId}/success', [StudentExamController::class, 'submitSuccess'])->where('examId', '[0-9]+')->name('studentExam.success');

});