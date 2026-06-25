<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\StudentExamController;
use App\Http\Controllers\QuestionBankController;
use Illuminate\Support\Facades\Route;

// Landing Page
Route::get('/', [AuthController::class, 'showWelcome'])->name('welcome');

// STUDENT ROUTES
Route::group(['prefix' => 'student'], function () {
    Route::get('/register', [AuthController::class, 'showStudentRegister'])->name('studentAuth.register');
    Route::post('/register', [AuthController::class, 'studentRegister'])->name('studentAuth.register.submit');
    
    Route::get('/login', [AuthController::class, 'showStudentLogin'])->name('studentAuth.login');
    Route::post('/login', [AuthController::class, 'studentLogin'])->name('studentAuth.login.submit');
    
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('studentAuth.forgotPassword');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('studentAuth.forgotPassword.submit');

    Route::group(['middleware' => 'check.student.auth'], function () {
        Route::get('/dashboard', [AuthController::class, 'studentDashboard'])->name('studentAuth.dashboard');
        Route::post('/logout', [AuthController::class, 'studentLogout'])->name('studentAuth.logout');
        Route::get('/results', [AuthController::class, 'studentResults'])->name('student.results');
        Route::get('/results/{take_id}', [AuthController::class, 'studentResultsShow'])->name('student.results.show');
        Route::get('/profile', [AuthController::class, 'showProfileForm'])->name('student.profile.edit');
        Route::put('/profile', [AuthController::class, 'updateProfile'])->name('student.profile.update');
    });
});

Route::group(['prefix' => 'student-exam', 'middleware' => 'check.student.auth'], function () {
    Route::get('/', [StudentExamController::class, 'examList'])->name('studentExam.list');
    Route::get('/{examId}/instructions', [StudentExamController::class, 'examInstructions'])->where('examId', '[0-9]+')->name('studentExam.instructions');
    Route::get('/{examId}/start', [StudentExamController::class, 'startExam'])->where('examId', '[0-9]+')->name('studentExam.start');
    Route::post('/{examId}/submit', [StudentExamController::class, 'submitExam'])->where('examId', '[0-9]+')->name('studentExam.submit');
    Route::get('/{examId}/success', [StudentExamController::class, 'submitSuccess'])->where('examId', '[0-9]+')->name('studentExam.success');
    Route::post('/exam/by-code', [StudentExamController::class, 'startByCode'])->name('student.exam.by-code');
});

// TEACHER ROUTES
Route::group(['prefix' => 'teacher'], function () {
    
    Route::get('/register', [AuthController::class, 'showTeacherRegister'])->name('teacherAuth.register');
    Route::post('/register', [AuthController::class, 'teacherRegister'])->name('teacherAuth.register.submit');
    
    Route::get('/login', [AuthController::class, 'showTeacherLogin'])->name('teacherAuth.login');
    Route::post('/login', [AuthController::class, 'teacherLogin'])->name('teacherAuth.login.submit');

    Route::get('/forgot-password', [AuthController::class, 'showTeacherForgotPassword'])->name('teacherAuth.forgotPassword');
    Route::post('/forgot-password', [AuthController::class, 'teacherForgotPassword'])->name('teacherAuth.forgotPassword.submit');

    Route::group(['middleware' => 'check.teacher.auth'], function () {
        
        Route::get('/dashboard', [AuthController::class, 'teacherDashboard'])->name('teacherAuth.dashboard');
        Route::post('/logout', [AuthController::class, 'teacherLogout'])->name('teacherAuth.logout');
        
        //For Create and edit exam
        Route::get('/exams/create', [ExamController::class, 'create'])->name('exams.create');
        Route::post('/exams/store', [ExamController::class, 'store'])->name('exams.store');
        Route::get('/exams/{exam_id}/edit', [ExamController::class, 'edit'])->where('exam_id', '[0-9]+')->name('exams.edit');
        Route::post('/exams/{exam_id}/update', [ExamController::class, 'update'])->where('exam_id', '[0-9]+')->name('exams.update');
        Route::post('/exams/{exam_id}/change-status', [ExamController::class, 'toggleStatus'])->where('exam_id', '[0-9]+')->name('exams.changeStatus');

        // Manage Questions for that Exam
        Route::get('/exams/{exam_id}/questions', [ExamController::class, 'manageQuestions'])->name('exams.questions');
        Route::post('/exams/{exam_id}/questions/action', [ExamController::class, 'handleQuestionAction'])->name('exams.questions.action');
        Route::group(['prefix' => 'question-bank'], function() {
            Route::get('/', [QuestionBankController::class, 'create'])->name('questions.create');
            Route::post('/store', [QuestionBankController::class, 'store'])->name('questions.store');
            Route::delete('/{id}', [QuestionBankController::class, 'destroy'])->name('questions.delete');
            Route::post('/subjects/store', [QuestionBankController::class, 'storeSubject'])->name('subjects.store');
            Route::post('/subjects/{id}', [QuestionBankController::class, 'updateSubject'])->name('subjects.update');
            Route::put('/subjects/{id}', [QuestionBankController::class, 'updateSubject'])->name('subjects.update');
            Route::delete('/subjects/{id}', [QuestionBankController::class, 'destroySubject'])->name('subjects.delete');

            Route::get('/results', [AuthController::class, 'teacherResultsIndex'])->name('teacher.results.index');
            Route::get('/exams/{exam}/results', [AuthController::class, 'teacherResults'])->name('teacher.results');
            Route::get('/results/{take}', [AuthController::class, 'teacherStudentResult'])->name('teacher.student.result');
            
        });
        
    });
});