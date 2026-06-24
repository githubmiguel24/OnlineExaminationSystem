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
    //STUDENT LOGIN or REGISTER
    public function showStudentRegister()
    {
        return view('student-auth.register');
    }

    public function studentRegister(Request $request)
    {
        $request->validate([
            'student_number' => ['required', 'string', 'max:50', 'unique:students,student_number'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

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

        $student = DB::table('students')->where('email', $request->email)->first();

        if (!$student || !Hash::check($request->password, $student->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput($request->only('email'));
        }

        Session::put('student', $student);
        return redirect()->route('studentAuth.dashboard')->with('success', 'Welcome back, ' . $student->name . '!');
    }

    public function studentLogout(Request $request)
    {
        Session::forget('student');
        $request->session()->regenerate();
        return redirect()->route('studentAuth.login')->with('success', 'You have been logged out.');
    }

    public function studentDashboard()
    {
        $student = Session::get('student');
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

    //FOR TEACHER LOGIN OR REGISTER
    public function showTeacherRegister()
    {
        return view('teacher-auth.register');
    }

    public function teacherRegister(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:teachers,email'], // Assuming teachers table
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $teacherId = DB::table('teachers')->insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $teacher = DB::table('teachers')->where('id', $teacherId)->first();
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
        
        $teacher = DB::table('teachers')
            ->where('email', $request->email)
            ->first();

        if (!$teacher || !Hash::check($request->password, $teacher->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput($request->only('email'));
        }

        Session::put('teacher', $teacher);
        return redirect()->route('teacherAuth.dashboard')->with('success', 'Welcome back, ' . $teacher->name . '!');
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