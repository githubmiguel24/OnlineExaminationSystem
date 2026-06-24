<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    // ==========================================
    // PAGE 1: CREATE EXAM & SETTINGS
    // ==========================================
    public function create()
    {
        $subjects = DB::table('subjects_table')->get();
        return view('teacher-auth.exam-create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $teacher = (object) Session::get('teacher');

        // Validation updated to match your exact columns
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|integer',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $status = $request->has('publish_immediately') ? 'Published' : 'Draft';

        // Insert into exams_table (matches your screenshot exactly)
        $examId = DB::table('exams_table')->insertGetId([
            'user_id' => $teacher->user_id, // Swapped teacher_id to user_id
            'subject_id' => $request->subject_id,
            'title' => $request->title, // Lowercase title
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $status,
            'access_code' => strtoupper(Str::random(6)),
        ]);

        return redirect()->route('exams.questions', ['exam_id' => $examId])
                         ->with('success', 'Exam settings saved! Now add your questions.');
    }

    // ==========================================
    // PAGE 2: MANAGE QUESTIONS 
    // ==========================================
    public function manageQuestions($exam_id)
    {
        $exam = DB::table('exams_table')->where('exam_id', $exam_id)->first();
        
        $attachedIds = DB::table('exam_question_table')->where('exam_id', $exam_id)->pluck('question_id')->toArray();
        $attachedQuestions = DB::table('questions_table')->whereIn('question_id', $attachedIds)->get(); // Added 's' to questions_table

        $availableQuestions = DB::table('questions_table') // Added 's' to questions_table
            ->where('subject_id', $exam->subject_id)
            ->whereNotIn('question_id', $attachedIds)
            ->get();

        return view('teacher-auth.exam-questions', compact('exam', 'attachedQuestions', 'availableQuestions'));
    }

    public function handleQuestionAction(Request $request, $exam_id)
    {
        $action = $request->submit_action;

        // ACTION A: Add existing questions from the bank
        if ($action === 'add_from_bank') {
            if ($request->has('questions')) {
                $inserts = [];
                foreach ($request->questions as $qId) {
                    $inserts[] = ['exam_id' => $exam_id, 'question_id' => $qId];
                }
                DB::table('exam_question_table')->insert($inserts);
                return back()->with('success', 'Questions added from bank!');
            }
            return back()->withErrors(['questions' => 'No questions were selected.']);
        }

        // ACTION B: Create a brand new question
        if ($action === 'create_new') {
            $exam = DB::table('exams_table')->where('exam_id', $exam_id)->first();
            
            $request->validate([
                'question_text' => 'required|string',
                'choice_a' => 'required|string',
                'choice_b' => 'required|string',
                'choice_c' => 'required|string',
                'choice_d' => 'required|string',
                'correct_answer' => 'required|string|in:A,B,C,D',
            ]);

            // Save to questions_table (Added 's')
            $newQId = DB::table('questions_table')->insertGetId([
                'subject_id' => $exam->subject_id,
                'quest_desc' => $request->question_text,
                'quest_choices' => json_encode(['A' => $request->choice_a, 'B' => $request->choice_b, 'C' => $request->choice_c, 'D' => $request->choice_d]),
                'quest_answer' => $request->correct_answer,
                'quest_pts' => 1
            ]);

            DB::table('exam_question_table')->insert(['exam_id' => $exam_id, 'question_id' => $newQId]);
            return back()->with('success', 'New question created and added!');
        }

        // ACTION C: Remove a question
        if ($action === 'remove_question') {
            DB::table('exam_question_table')
                ->where('exam_id', $exam_id)
                ->where('question_id', $request->remove_id)
                ->delete();
            return back()->with('success', 'Question removed from exam.');
        }
    }
}