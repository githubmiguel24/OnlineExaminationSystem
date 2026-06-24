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
            ->where('role_id', 2)
            ->first();

        if (!$student) {
            return back()->withErrors(['email' => 'No matching student account found.'])->withInput($request->only('email'));
        }

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

    public function teacherDashboard(Request $request)
    {
        $teacherSession = session('teacher');

        if (!$teacherSession) {
            return redirect()->route('teacherAuth.login')
                ->withErrors(['login' => 'Please login first.']);
        }

        $teacherId = $teacherSession['user_id'];
        $search = $request->input('search', '');

        $teacher = DB::table('users_table')
            ->where('user_id', $teacherId)
            ->first();

        $exams = DB::table('exams_table as e')
            ->join('subjects_table as s', 's.subject_id', '=', 'e.subject_id')
            ->where('e.user_id', $teacherId)
            ->select(
                'e.exam_id',
                'e.title',
                'e.description',
                'e.duration_minutes',
                'e.status',
                'e.access_code',
                'e.start_date',
                'e.end_date',
                's.subject_displayname as subject_name',
                DB::raw('(SELECT COUNT(*) FROM exam_question_table eq WHERE eq.exam_id = e.exam_id) as question_count'),
                DB::raw('(SELECT COUNT(*) FROM student_exam_table se WHERE se.exam_id = e.exam_id) as taker_count')
            )
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('e.title', 'like', "%{$search}%")
                    ->orWhere('s.subject_name', 'like', "%{$search}%")
                    ->orWhere('s.subject_displayname', 'like', "%{$search}%");
                });
            })
            ->orderBy('e.exam_id', 'desc')
            ->get();

        $totalStudents = DB::table('student_exam_table as se')
            ->join('exams_table as e', 'e.exam_id', '=', 'se.exam_id')
            ->where('e.user_id', $teacherId)
            ->distinct()
            ->count('se.student_id');

        $totalExams = DB::table('exams_table')
            ->where('user_id', $teacherId)
            ->count();

        $totalSubjects = DB::table('exams_table')
            ->where('user_id', $teacherId)
            ->distinct()
            ->count('subject_id');

        return view('teacher-auth.dashboard', compact(
            'teacher',
            'exams',
            'totalStudents',
            'totalExams',
            'totalSubjects',
            'search'
        ));
    }

    public function studentDashboard(Request $request)
    {
        $studentSession = session('student');
        if (!$studentSession) {
            return redirect()->route('studentAuth.login')
                ->withErrors(['login' => 'Please login first.']);
        }

        $studentId = $studentSession['user_id'];
        $search    = $request->input('search', '');

        $student = DB::table('users_table')
            ->where('user_id', $studentId)
            ->first();

        $allPublishedExams = DB::table('exams_table as e')
            ->join('subjects_table as s', 's.subject_id', '=', 'e.subject_id')
            ->where('e.status', 'Published')
            ->select(
                'e.exam_id',
                'e.title',
                'e.description',
                'e.duration_minutes',
                'e.access_code',
                's.subject_displayname as subject_name'
            )
            ->when($search, function ($query) use ($search) {
                $query->where('e.title', 'like', "%{$search}%")
                    ->orWhere('s.subject_name', 'like', "%{$search}%");
            })
            ->orderBy('e.exam_id', 'desc')
            ->get();

        $takenExamIds = DB::table('student_exam_table')
            ->where('student_id', $studentId)
            ->pluck('exam_id')
            ->toArray();

        $availableExams = $allPublishedExams->filter(function ($exam) use ($takenExamIds) {
            return !in_array($exam->exam_id, $takenExamIds);
        })->values();

        $recentResults = DB::table('student_exam_table as se')
            ->join('exams_table as e', 'e.exam_id', '=', 'se.exam_id')
            ->join('subjects_table as s', 's.subject_id', '=', 'e.subject_id')
            ->where('se.student_id', $studentId)
            ->whereNotNull('se.end_time')
            ->select(
                'se.take_id',
                'se.exam_id',
                'se.score',
                'se.status',
                'e.title as exam_title',
                's.subject_displayname as subject_name',
                'se.end_time'
            )
            ->orderBy('se.end_time', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $total = DB::table('exam_question_table')
                    ->where('exam_id', $row->exam_id)
                    ->count();
                $row->total = $total;
                $row->percentage = ($total > 0) ? round(($row->score / $total) * 100) : 0;
                if (is_null($row->status)) {
                    $row->status = $row->percentage >= 75 ? 'Passed' : 'Failed';
                }
                return $row;
            });

        $totalExams     = $allPublishedExams->count();
        $examsTaken     = count($takenExamIds);
        $examsRemaining = max(0, $totalExams - $examsTaken);

        return view('student-auth.dashboard', compact(
            'student',
            'availableExams',
            'recentResults',
            'search',
            'totalExams',
            'examsTaken',
            'examsRemaining'
        ));
    }

    public function examByCode(Request $request)
    {
        $request->validate([
            'access_code' => 'required|string'
        ]);

        $student = session('student');
        if (!$student) {
            return redirect()->route('studentAuth.login');
        }

        $exam = DB::table('exams_table')
            ->where('access_code', $request->access_code)
            ->first();

        if (!$exam) {
            return back()->with('error', 'Invalid exam code. Please try again.');
        }

        // Check if student already took this exam
        $taken = DB::table('student_exam_table')
            ->where('student_id', $student['user_id'])
            ->where('exam_id', $exam->exam_id)
            ->exists();

        if ($taken) {
            return back()->with('error', 'You have already taken this exam.');
        }

        return redirect()->route('student.exam.instructions', $exam->exam_id);
    }

    public function studentResults()
    {
        $student = session('student');
        if (!$student) {
            return redirect()->route('studentAuth.login');
        }

        $studentId = $student['user_id'];

        $results = DB::table('student_exam_table as se')
            ->join('exams_table as e', 'e.exam_id', '=', 'se.exam_id')
            ->join('subjects_table as s', 's.subject_id', '=', 'e.subject_id')
            ->where('se.student_id', $studentId)
            ->whereNotNull('se.end_time')
            ->select(
                'se.take_id',
                'se.exam_id',
                'se.score',
                'se.status',
                'e.title as exam_title',
                's.subject_displayname as subject_name',
                'se.end_time'
            )
            ->orderBy('se.end_time', 'desc')
            ->get()
            ->map(function ($row) {
                $total = DB::table('exam_question_table')
                    ->where('exam_id', $row->exam_id)
                    ->count();
                $row->total = $total;
                $row->percentage = ($total > 0) ? round(($row->score / $total) * 100) : 0;
                if (is_null($row->status)) {
                    $row->status = $row->percentage >= 75 ? 'Passed' : 'Failed';
                }
                return $row;
            });

        return view('student-auth.results', compact('results'));
    }

    public function studentResultsShow($take_id)
    {
        $student = session('student');
        if (!$student) {
            return redirect()->route('studentAuth.login');
        }

        // Get the exam result
        $result = DB::table('student_exam_table as se')
            ->join('exams_table as e', 'e.exam_id', '=', 'se.exam_id')
            ->join('subjects_table as s', 's.subject_id', '=', 'e.subject_id')
            ->where('se.take_id', $take_id)
            ->where('se.student_id', $student['user_id'])
            ->select(
                'se.*',
                'e.title as exam_title',
                'e.description as exam_description',
                's.subject_displayname as subject_name'
            )
            ->first();

        if (!$result) {
            abort(404, 'Result not found.');
        }

        // Get total questions
        $total = DB::table('exam_question_table')
            ->where('exam_id', $result->exam_id)
            ->count();
        $result->total = $total;
        $result->percentage = ($total > 0) ? round(($result->score / $total) * 100) : 0;
        if (is_null($result->status)) {
            $result->status = $result->percentage >= 75 ? 'Passed' : 'Failed';
        }

        // Fetch questions
        $questions = DB::table('exam_question_table as eq')
            ->join('questions_table as q', 'q.question_id', '=', 'eq.question_id')
            ->leftJoin('student_answers_table as sa', function ($join) use ($take_id) {
                $join->on('sa.question_id', '=', 'q.question_id')
                    ->where('sa.take_id', '=', $take_id);
            })
            ->where('eq.exam_id', $result->exam_id)
            ->select('q.*', 'sa.answer as student_answer')
            ->get();

        // Decode choices
        foreach ($questions as $q) {
            $q->choices = json_decode($q->quest_choices, true);
        }

        return view('student-auth.result-detail', compact('result', 'questions'));
    }

    public function teacherResults(Request $request, $examId)
    {
        $teacher = Session::get('teacher');
        if (!$teacher) {
            return redirect()->route('teacherAuth.login');
        }

        $teacherId = $teacher['user_id'];
        $search    = $request->input('search', '');

        // Verify exam ownership
        $exam = DB::table('exams_table as e')
            ->join('subjects_table as s', 's.subject_id', '=', 'e.subject_id')
            ->where('e.exam_id', $examId)
            ->where('e.user_id', $teacherId)
            ->select('e.exam_id', 'e.title as exam_title', 's.subject_displayname as subject_name')
            ->first();

        if (!$exam) {
            abort(404, 'Exam not found or you do not own it.');
        }

        // Fetch all attempts for this exam
        $results = DB::table('student_exam_table as se')
            ->join('users_table as u', 'u.user_id', '=', 'se.student_id')
            ->where('se.exam_id', $examId)
            ->select(
                'se.take_id',
                'se.student_id',
                'se.score',
                'se.status',
                'se.end_time',
                'u.user_id as student_id',      
                'u.full_name',
                'u.email'
            )
            ->when($search, function ($query) use ($search) {
                $query->where('u.full_name', 'like', "%{$search}%")
                    ->orWhere('u.email', 'like', "%{$search}%");
            })
            ->orderBy('se.end_time', 'desc')
            ->get()
            ->map(function ($row) use ($examId) {
                $total = DB::table('exam_question_table')
                    ->where('exam_id', $examId)
                    ->count();
                $row->total = $total;
                $row->percentage = ($total > 0) ? round(($row->score / $total) * 100) : 0;
                if (is_null($row->status)) {
                    $row->status = $row->percentage >= 75 ? 'Passed' : 'Failed';
                }
                return $row;
            });

            $passCount = $results->where('status', 'Passed')->count();
            $failCount = $results->where('status', 'Failed')->count();
            $avgScore  = $results->count() > 0 ? round($results->avg('percentage')) : 0;

            return view('teacher-auth.results', compact('exam', 'results', 'search', 'passCount', 'failCount', 'avgScore'));    
        }

    public function teacherStudentResult($takeId)
    {
        $teacher = Session::get('teacher');
        if (!$teacher) {
            return redirect()->route('teacherAuth.login');
        }

        $teacherId = $teacher['user_id'];

        $take = DB::table('student_exam_table as se')
            ->join('exams_table as e', 'e.exam_id', '=', 'se.exam_id')
            ->join('subjects_table as s', 's.subject_id', '=', 'e.subject_id')
            ->join('users_table as u', 'u.user_id', '=', 'se.student_id')
            ->where('se.take_id', $takeId)
            ->where('e.user_id', $teacherId)
            ->select(
                'se.take_id',
                'se.exam_id',
                'se.start_time',
                'se.end_time',
                'se.duration',
                'se.score',
                'se.status',
                'e.title as exam_title',
                'e.duration_minutes',
                's.subject_displayname as subject_name',
                'u.user_id as student_id',
                'u.full_name',
                'u.email',
                'u.email'
            )
            ->first();

        if (!$take) {
            abort(404, 'Result not found or you do not have permission.');
        }

        $total = (int) DB::table('exam_question_table')
            ->where('exam_id', $take->exam_id)
            ->count();

        $score = (int) ($take->score ?? 0);
        $percentage = ($total > 0) ? round(($score / $total) * 100) : 0;
        $status = $take->status ?? ($percentage >= 75 ? 'Passed' : 'Failed');

        $questions = DB::table('exam_question_table as eq')
            ->join('questions_table as q', 'q.question_id', '=', 'eq.question_id')
            ->leftJoin('student_answers_table as sa', function ($join) use ($takeId) {
                $join->on('sa.question_id', '=', 'q.question_id')
                    ->where('sa.take_id', '=', $takeId);
            })
            ->where('eq.exam_id', $take->exam_id)
            ->select(
                'q.question_id',
                'q.quest_desc',
                'q.quest_choices',
                'q.quest_answer',
                'q.quest_pts',
                'sa.answer as student_answer'
            )
            ->orderBy('q.question_id')
            ->get()
            ->map(function ($q) {
                $q->choices = json_decode($q->quest_choices, true) ?? [];
                $q->student_answer = $q->student_answer ?? null;
                $q->is_correct = ($q->student_answer && $q->student_answer == $q->quest_answer);
                $q->quest_pts = (int) ($q->quest_pts ?? 1);
                $q->earned = $q->is_correct ? $q->quest_pts : 0;
                return $q;
            });

        $examId = $take->exam_id;

        return view('teacher-auth.student-result', compact(
            'take',
            'score',
            'total',
            'percentage',
            'status',
            'questions',
            'examId'
        ));
    }

    public function teacherResultsIndex()
    {
        $teacher = Session::get('teacher');
        if (!$teacher) {
            return redirect()->route('teacherAuth.login');
        }

        $teacherId = $teacher['user_id'];

        $exams = DB::table('exams_table as e')
            ->join('subjects_table as s', 's.subject_id', '=', 'e.subject_id')
            ->leftJoin('student_exam_table as se', 'se.exam_id', '=', 'e.exam_id')
            ->where('e.user_id', $teacherId)
            ->select(
                'e.exam_id',
                'e.title',
                'e.description',
                's.subject_displayname as subject_name',
                DB::raw('COUNT(DISTINCT se.take_id) as takers'),
                DB::raw('AVG(se.score) as avg_score'),
                DB::raw('SUM(CASE WHEN se.status = "Passed" THEN 1 ELSE 0 END) as passed'),
                DB::raw('COUNT(se.take_id) as total_attempts')
            )
            ->groupBy('e.exam_id', 'e.title', 'e.description', 's.subject_displayname')
            ->orderBy('e.exam_id', 'desc')
            ->get()
            ->map(function ($exam) {
                $exam->avg_score = $exam->takers > 0 ? round($exam->avg_score, 1) : 0;
                $exam->pass_rate = $exam->takers > 0 ? round(($exam->passed / $exam->takers) * 100) : 0;
                return $exam;
            });

        return view('teacher-auth.results-index', compact('exams'));
    }
}