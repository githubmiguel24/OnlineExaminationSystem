<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    private function getTeacherId()
    {
        $teacher = session('teacher');
        if (!$teacher) {
            return null;
        }
        return $teacher['user_id'];
    }

    private function getExamWithOwnerCheck($examId)
    {
        $teacherId = $this->getTeacherId();
        return DB::table('exams_table')
            ->where('exam_id', $examId)
            ->where('user_id', $teacherId)
            ->first();
    }

    public function create()
    {
        $subjects = DB::table('subjects_table')->get();
        return view('teacher-auth.exam-create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $teacher = (object) Session::get('teacher');

        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|integer',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required|date_format:H:i',
        ]);

        if ($request->start_date === $request->end_date && $request->end_time <= $request->start_time) {
            return back()->withErrors(['end_time' => 'The end time must be after the start time when the exam starts and ends on the same day.'])->withInput();
        }

        $status = $request->has('publish_immediately') ? 'Published' : 'Draft';

        $examId = DB::table('exams_table')->insertGetId([
            'user_id' => $teacher->user_id,
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            'status' => $status,
            'access_code' => strtoupper(Str::random(6)),
        ]);

        return redirect()->route('exams.questions', ['exam_id' => $examId])
                         ->with('success', 'Exam settings saved! Now add your questions.');
    }

    public function edit($exam_id)
    {
        $exam = $this->getExamWithOwnerCheck($exam_id);
        if (!$exam) {
            return redirect()->route('teacherAuth.dashboard')
                ->with('error', 'Exam not found or you do not have permission to edit this exam.');
        }

        $subjects = DB::table('subjects_table')->get();
        return view('teacher-auth.exam-create', compact('subjects', 'exam'));
    }

    public function update(Request $request, $exam_id)
    {
        $exam = $this->getExamWithOwnerCheck($exam_id);
        if (!$exam) {
            return redirect()->route('teacherAuth.dashboard')
                ->with('error', 'Exam not found or you do not have permission to update this exam.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|integer',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required|date_format:H:i',
            'publish_immediately' => 'nullable|in:1',
        ]);

        if ($request->start_date === $request->end_date && $request->end_time <= $request->start_time) {
            return back()->withErrors(['end_time' => 'The end time must be after the start time when the exam starts and ends on the same day.'])->withInput();
        }

        $status = $request->has('publish_immediately') ? 'Published' : 'Draft';

        DB::table('exams_table')
            ->where('exam_id', $exam_id)
            ->update([
                'subject_id' => $request->subject_id,
                'title' => $request->title,
                'description' => $request->description,
                'duration_minutes' => $request->duration_minutes,
                'start_date' => $request->start_date,
                'start_time' => $request->start_time,
                'end_date' => $request->end_date,
                'end_time' => $request->end_time,
                'status' => $status,
            ]);

        return redirect()->route('teacherAuth.dashboard')
            ->with('success', 'Exam updated successfully.');
    }

    public function toggleStatus(Request $request, $exam_id)
    {
        $exam = $this->getExamWithOwnerCheck($exam_id);
        if (!$exam) {
            return redirect()->route('teacherAuth.dashboard')
                ->with('error', 'Exam not found or you do not have permission to change this exam.');
        }

        $newStatus = $exam->status === 'Published' ? 'Draft' : 'Published';
        DB::table('exams_table')
            ->where('exam_id', $exam_id)
            ->update(['status' => $newStatus]);

        return back()->with('success', 'Exam status changed to ' . $newStatus . '.');
    }

    public function manageQuestions($exam_id)
    {
        $exam = $this->getExamWithOwnerCheck($exam_id);
        
        if (!$exam) {
            return redirect()->route('teacherAuth.dashboard')
                ->with('error', 'Exam not found or you do not have permission to manage this exam.');
        }
        
        $attachedIds = DB::table('exam_question_table')->where('exam_id', $exam_id)->pluck('question_id')->toArray();
        $attachedQuestions = DB::table('questions_table')->whereIn('question_id', $attachedIds)->get();

        $availableQuestions = DB::table('questions_table')
            ->where('subject_id', $exam->subject_id)
            ->whereNotIn('question_id', $attachedIds)
            ->get();

        return view('teacher-auth.exam-questions', compact('exam', 'attachedQuestions', 'availableQuestions'));
    }

    public function handleQuestionAction(Request $request, $exam_id)
    {
        $exam = $this->getExamWithOwnerCheck($exam_id);
        
        if (!$exam) {
            return redirect()->route('teacherAuth.dashboard')
                ->with('error', 'Exam not found or you do not have permission to modify this exam.');
        }

        $action = $request->submit_action;

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

        if ($action === 'create_new') {
            $request->validate([
                'question_text' => 'required|string',
                'choice_a' => 'required|string',
                'choice_b' => 'required|string',
                'choice_c' => 'required|string',
                'choice_d' => 'required|string',
                'correct_answer' => 'required|string|in:A,B,C,D',
            ]);

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

        if ($action === 'remove_question') {
            DB::table('exam_question_table')
                ->where('exam_id', $exam_id)
                ->where('question_id', $request->remove_id)
                ->delete();
            return back()->with('success', 'Question removed from exam.');
        }
    }
}