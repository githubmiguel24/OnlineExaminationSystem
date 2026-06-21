<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class StudentExamController extends Controller
{
    // 1. Display all available exams to the student
    public function examList()
    {
        $studentId = $this->getStudentId();

        $exams = DB::table('exams')
            ->where('status', 'published')
            ->get();

        $takenExamIds = DB::table('student_answers')
            ->where('student_id', $studentId)
            ->distinct()
            ->pluck('exam_id')
            ->toArray();

        return view('student-exam.exam-list', compact('exams', 'takenExamIds'));
    }

    // 2. Show exam instructions before the student starts
    public function examInstructions($examId)
    {
        $exam = $this->getPublishedExam($examId);

        if (!$exam) {
            return redirect()->route('studentExam.list')->with('error', 'Exam not found.');
        }

        $studentId = $this->getStudentId();
        $alreadySubmitted = $this->hasSubmitted($studentId, $examId);

        return view('student-exam.exam-instructions', compact('exam', 'alreadySubmitted'));
    }

    // 3 & 4. Start the exam: load all questions + choices and show the exam form
    public function startExam($examId)
    {
        $exam = $this->getPublishedExam($examId);

        if (!$exam) {
            return redirect()->route('studentExam.list')->with('error', 'Exam not found.');
        }

        $studentId = $this->getStudentId();

        // Check attempt
        if ($this->hasSubmitted($studentId, $examId)) {
            return redirect()->route('studentExam.list')->with('error', 'You have already submitted this exam.');
        }

        $questions = DB::table('questions')
            ->where('exam_id', $examId)
            ->get();

        foreach ($questions as $question) {
            $question->choices = DB::table('choices')
                ->where('question_id', $question->id)
                ->get();
        }

        // Timer logic
        Session::put('exam_start_time_' . $examId, now());

        Log::info('Student ' . $studentId . ' started exam ' . $examId);

        return view('student-exam.take-exam', compact('exam', 'questions'));
    }

    // 6 & 7. Submit the exam and save all answers to the database
    public function submitExam(Request $request, $examId)
    {
        $exam = $this->getPublishedExam($examId);

        if (!$exam) {
            return redirect()->route('studentExam.list')->with('error', 'Exam not found.');
        }

        $studentId = $this->getStudentId();

        // Check attempt
        if ($this->hasSubmitted($studentId, $examId)) {
            return redirect()->route('studentExam.list')->with('error', 'You have already submitted this exam.');
        }

        // Validation
        $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'integer'],
        ], [
            'answers.required' => 'Please answer at least one question before submitting.',
        ]);

        $validQuestionIds = DB::table('questions')->where('exam_id', $examId)->pluck('id')->toArray();

        Log::info('Student ' . $studentId . ' submitting exam ' . $examId);

        // Save answers
        DB::transaction(function () use ($request, $studentId, $examId, $validQuestionIds) {
            foreach ($request->answers as $questionId => $choiceId) {

                if (!in_array($questionId, $validQuestionIds)) {
                    continue;
                }

                $validChoiceIds = DB::table('choices')->where('question_id', $questionId)->pluck('id')->toArray();
                if (!in_array($choiceId, $validChoiceIds)) {
                    continue;
                }

                DB::table('student_answers')->insert([
                    'student_id' => $studentId,
                    'exam_id' => $examId,
                    'question_id' => $questionId,
                    'choice_id' => $choiceId,
                    'answered_at' => now(),
                ]);
            }
        });

        Session::forget('exam_start_time_' . $examId);

        return redirect()->route('studentExam.success', $examId);
    }

    // 10. Basic success page after submission
    public function submitSuccess($examId)
    {
        $exam = DB::table('exams')->where('id', $examId)->first();

        if (!$exam) {
            return redirect()->route('studentExam.list')->with('error', 'Exam not found.');
        }

        return view('student-exam.submit-success', compact('exam'));
    }

    private function getPublishedExam($examId)
    {
        return DB::table('exams')->where('id', $examId)->where('status', 'published')->first();
    }

    private function hasSubmitted($studentId, $examId)
    {
        return DB::table('student_answers')
            ->where('student_id', $studentId)
            ->where('exam_id', $examId)
            ->exists();
    }

    private function getStudentId()
    {
        // Auth guaranteed by middleware
        return Session::get('student')->id;
    }
}