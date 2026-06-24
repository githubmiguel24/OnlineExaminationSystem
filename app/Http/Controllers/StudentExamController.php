<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class StudentExamController extends Controller
{
    private function getStudentId()
    {
        $student = session('student');
        if (!$student) {
            return null;
        }
        return $student['user_id'];
    }

    private function hasSubmitted($studentId, $examId)
    {
        return DB::table('student_exam_table')
            ->where('student_id', $studentId)
            ->where('exam_id', $examId)
            ->exists();
    }

    private function getPublishedExam($examId)
    {
        return DB::table('exams_table as e')
            ->join('subjects_table as s', 's.subject_id', '=', 'e.subject_id')
            ->where('e.exam_id', $examId)
            ->where('e.status', 'Published')
            ->select('e.*', 's.subject_displayname as subject_name')
            ->first();
    }

    public function examList()
    {
        $studentId = $this->getStudentId();
        if (!$studentId) {
            return redirect()->route('studentAuth.login');
        }

        $exams = DB::table('exams_table')
            ->where('status', 'Published')
            ->get();

        $takenExamIds = DB::table('student_exam_table')
            ->where('student_id', $studentId)
            ->distinct()
            ->pluck('exam_id')
            ->toArray();

        return view('student-exam.exam-list', compact('exams', 'takenExamIds'));
    }

    public function examInstructions($examId)
    {
        $exam = $this->getPublishedExam($examId);
        if (!$exam) {
            return redirect()->route('studentAuth.dashboard')
                ->with('error', 'Exam not found or not published.');
        }

        $studentId = $this->getStudentId();
        $alreadySubmitted = $this->hasSubmitted($studentId, $examId);

        return view('student-exam.exam-instructions', compact('exam', 'alreadySubmitted'));
    }

    public function startExam($examId)
    {
        $exam = $this->getPublishedExam($examId);
        if (!$exam) {
            return redirect()->route('studentAuth.dashboard')
                ->with('error', 'Exam not found or not published.');
        }

        $studentId = $this->getStudentId();
        if ($this->hasSubmitted($studentId, $examId)) {
            return redirect()->route('studentAuth.dashboard')
                ->with('error', 'You have already submitted this exam.');
        }

        $questionIds = DB::table('exam_question_table')
            ->where('exam_id', $examId)
            ->pluck('question_id')
            ->toArray();

        $questions = DB::table('questions_table')
            ->whereIn('question_id', $questionIds)
            ->get();

        foreach ($questions as $q) {
            $q->choices = json_decode($q->quest_choices, true) ?? [];
        }

        Session::put('exam_start_time_' . $examId, now());

        return view('student-exam.take-exam', compact('exam', 'questions'));
    }

    public function submitExam(Request $request, $examId)
    {
        $exam = $this->getPublishedExam($examId);
        if (!$exam) {
            return redirect()->route('studentAuth.dashboard')
                ->with('error', 'Exam not found or not published.');
        }

        $studentId = $this->getStudentId();
        if ($this->hasSubmitted($studentId, $examId)) {
            return redirect()->route('studentAuth.dashboard')
                ->with('error', 'You have already submitted this exam.');
        }

        $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'string'],
        ], [
            'answers.required' => 'Please answer at least one question before submitting.',
        ]);

        $validQuestionIds = DB::table('exam_question_table')
            ->where('exam_id', $examId)
            ->pluck('question_id')
            ->toArray();

        $takeId = DB::table('student_exam_table')->insertGetId([
            'student_id' => $studentId,
            'exam_id'    => $examId,
            'start_time' => Session::get('exam_start_time_' . $examId, now()),
            'end_time'   => now(),
            'duration'   => $exam->duration_minutes,
            'status'     => 'Pending',
            'score'      => 0,
        ]);

        // Process each answer
        $score = 0;
        $total = count($validQuestionIds);

        foreach ($request->answers as $questionId => $selectedOption) {
            if (!in_array($questionId, $validQuestionIds)) {
                continue;
            }

            // Insert into student_answers_table using take_id
            DB::table('student_answers_table')->insert([
                'take_id'      => $takeId,
                'question_id'  => $questionId,
                'answer'       => $selectedOption,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            $question = DB::table('questions_table')->where('question_id', $questionId)->first();
            if ($question && $question->quest_answer == $selectedOption) {
                $score++;
            }
        }

        // Update attempt with final score and status
        $status = ($score / max($total, 1) >= 0.75) ? 'Passed' : 'Failed';
        DB::table('student_exam_table')
            ->where('take_id', $takeId)
            ->update([
                'score'  => $score,
                'status' => $status,
            ]);

        Session::forget('exam_start_time_' . $examId);

        return redirect()->route('student.results.show', $takeId)
            ->with('success', 'Exam submitted successfully!');
    }

    public function submitSuccess($examId)
    {
        $exam = DB::table('exams_table')
            ->where('exam_id', $examId)
            ->first();

        if (!$exam) {
            return redirect()->route('studentAuth.dashboard')
                ->with('error', 'Exam not found.');
        }

        return view('student-exam.submit-success', compact('exam'));
    }

    public function startByCode(Request $request)
    {
        $request->validate([
            'access_code' => 'required|string'
        ]);

        $exam = DB::table('exams_table')
            ->where('access_code', $request->access_code)
            ->where('status', 'Published')
            ->first();

        if (!$exam) {
            return back()->withErrors(['access_code' => 'Invalid access code or exam not published.']);
        }

        return redirect()->route('studentExam.instructions', $exam->exam_id);
    }
}