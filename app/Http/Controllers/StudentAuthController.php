<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class StudentAuthController extends Controller
{
    // Show register form
    public function showRegister()
    {
        return view('student-auth.register');
    }

    public function register(Request $request)
    {
        // Validation
        $request->validate([
            'student_number' => ['required', 'string', 'max:50', 'unique:students,student_number'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // Create student
        $studentId = DB::table('students')->insertGetId([
            'student_number' => $request->student_number,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $student = DB::table('students')->where('id', $studentId)->first();

        Session::put('student', $student);

        return redirect()->route('studentAuth.dashboard')->with('success', 'Registration successful. Welcome!');
    }

    // Show login form
    public function showLogin()
    {
        return view('student-auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $student = DB::table('students')->where('email', $request->email)->first();

        // Check password
        if (!$student || !Hash::check($request->password, $student->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput($request->only('email'));
        }

        Session::put('student', $student);

        return redirect()->route('studentAuth.dashboard')->with('success', 'Welcome back, ' . $student->name . '!');
    }

    public function logout(Request $request)
    {
        Session::forget('student');
        $request->session()->regenerate();

        return redirect()->route('studentAuth.login')->with('success', 'You have been logged out.');
    }

    public function dashboard()
    {
        $student = Session::get('student');

        // Exam stats
        $totalExams = DB::table('exams')->where('status', 'published')->count();

        $takenExams = DB::table('student_answers')
            ->where('student_id', $student->id)
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
            'student_number' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $student = DB::table('students')
            ->where('student_number', $request->student_number)
            ->where('email', $request->email)
            ->first();

        if (!$student) {
            return back()->withErrors(['email' => 'No matching student found.'])->withInput($request->only('student_number', 'email'));
        }

        DB::table('students')->where('id', $student->id)->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('studentAuth.login')->with('success', 'Password reset. Please log in.');
    }
}