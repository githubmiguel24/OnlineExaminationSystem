<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentExamController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionBankController;

// Student auth routes
Route::get('/', [AuthController::class, 'showWelcome'])->name('welcome');
Route::group(['prefix' => 'user'], function () {

    Route::get('/register', [AuthController::class, 'showRegister'])->name('studentAuth.register');
    Route::post('/register', [AuthController::class, 'register'])->name('studentAuth.register.submit');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('studentAuth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('studentAuth.login.submit');
    Route::get('/forgot-password', [authController::class, 'showForgotPassword'])->name('studentAuth.forgotPassword');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('studentAuth.forgotPassword.submit');

    Route::group(['middleware' => 'check.student.auth'], function () {
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('studentAuth.dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('studentAuth.logout');
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

Route::group(['prefix' => 'question-bank'], function() {
    // View the bank dashboard & handle category search/filtering
    Route::get('/', [QuestionBankController::class, 'index'])->name('questions.index');
    
    // Add a question
    Route::post('/store', [QuestionBankController::class, 'store'])->name('questions.store');
    
    // Delete a question
    Route::delete('/delete/{id}', [QuestionBankController::class, 'destroy'])->name('questions.delete');
});