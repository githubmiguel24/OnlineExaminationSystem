<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showWelcome()
    {
        return view('welcome');
    }

    // STUDENT LOGIN & REGISTER
    public function showStudentRegister()
    {
        return view('student-auth.register');
    }

    public function studentRegister(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users_table,email'], // Ensure uniqueness
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $studentId = DB::table('users_table')->insertGetId([
            'role_id' => 1, 
            'full_name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $student = DB::table('users_table')->where('user_id', $studentId)->first();
        Session::put('student', $student);

        return redirect()->route('studentAuth.dashboard')->with('success', 'Registration successful. Welcome!');
    }

    public function showStudentLogin()
    {
        return view('student-auth.login');
    }

    public function studentLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $student = DB::table('users_table')->where('email', $request->email)->first();

        if (!$student || !Hash::check($request->password, $student->password) || $student->role_id != 2) {
            return back()->withErrors(['email' => 'Invalid email, password, or unauthorized portal access.'])->withInput($request->only('email'));
        }

        Session::put('student', $student);
        
        return redirect()->route('studentAuth.dashboard')->with('success', 'Welcome back, ' . $student->full_name . '!');
    }

    public function studentLogout(Request $request)
    {
        Session::forget('student');
        $request->session()->regenerate();
        return redirect()->route('studentAuth.login')->with('success', 'You have been logged out.');
    }

    public function studentDashboard()
    {
        $student = (object) Session::get('student');
        
        $totalExams = DB::table('exams_table')->count();

        $takenExams = DB::table('student_exam_table')
            ->where('student_id', $student->user_id) 
            ->distinct()
            ->pluck('exam_id')
            ->count();

        return view('student-auth.dashboard', compact('student', 'totalExams', 'takenExams'));
    }

    public function showForgotPassword()
    {
        return view('student-auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $student = DB::table('users_table')
            ->where('email', $request->email)
            ->where('role_id', 2) // Ensure they are a student
            ->first();

        if (!$student) {
            return back()->withErrors(['email' => 'No matching student account found.'])->withInput($request->only('email'));
        }

        // Used user_id
        DB::table('users_table')->where('user_id', $student->user_id)->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('studentAuth.login')->with('success', 'Password reset. Please log in.');
    }

    //TEACHER LOGIN & REGISTER
    public function showTeacherRegister()
    {
        return view('teacher-auth.register');
    }

    public function teacherRegister(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users_table,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $teacherId = DB::table('users_table')->insertGetId([
            'role_id' => 2, 
            'full_name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Used user_id
        $teacher = DB::table('users_table')->where('user_id', $teacherId)->first();
        Session::put('teacher', $teacher);

        return redirect()->route('teacherAuth.dashboard')->with('success', 'Teacher Registration successful.');
    }

    public function showTeacherLogin()
    {
        return view('teacher-auth.login');
    }

    public function teacherLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $teacher = DB::table('users_table')->where('email', $request->email)->first();

        if (!$teacher || !Hash::check($request->password, $teacher->password) || $teacher->role_id != 1) {
            return back()->withErrors(['email' => 'Invalid email, password, or unauthorized portal access.'])->withInput($request->only('email'));
        }

        Session::put('teacher', $teacher);
        
        return redirect()->route('teacherAuth.dashboard')->with('success', 'Welcome back, ' . $teacher->full_name . '!');
    }

    public function teacherLogout(Request $request)
    {
        Session::forget('teacher');
        $request->session()->regenerate();
        return redirect()->route('teacherAuth.login')->with('success', 'You have been logged out.');
    }

    public function teacherDashboard()
    {
        $teacher = Session::get('teacher');
        return view('teacher-auth.dashboard', compact('teacher'));
    }
}