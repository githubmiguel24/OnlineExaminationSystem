<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    public function create(Request $request)
    {
        // Get the current step from the URL (default to 1)
        $step = $request->query('step', 1);

        // Get whatever data has been saved in the session so far
        $examData = session('exam_data', []);

        // Step 1 Needs Subjects
        $subjects = DB::table('subjects_table')->get();
        
        // Step 2 & 3 Need Subject Name and Questions (Filtered by what was picked in Step 1)
        $questions = [];
        $selectedSubjectName = 'Unknown Subject';

        if ($step >= 2 && isset($examData['subject_id'])) {
            $questions = DB::table('question_table')
                            ->where('subject_id', $examData['subject_id'])
                            ->get();
                            
            $subjectRecord = DB::table('subjects_table')
                            ->where('subject_id', $examData['subject_id'])
                            ->first();
                            
            if ($subjectRecord) {
                $selectedSubjectName = $subjectRecord->subject_name;
            }
        }

        return view('teacher-auth.create-exam', compact('step', 'subjects', 'questions', 'examData', 'selectedSubjectName'));
    }

    public function processStep(Request $request)
    {
        $currentStep = $request->input('current_step');
        $action = $request->input('action'); // Will be 'next', 'back', or 'publish'

        $examData = session('exam_data', []);

        // If user clicked "Previous"
        if ($action == 'back') {
            return redirect()->route('exams.create', ['step' => $currentStep - 1]);
        }

        // Processing STEP 1 -> Going to Step 2
        if ($currentStep == 1) {
            $request->validate([
                'title' => 'required|string|max:255',
                'subject_id' => 'required|integer',
                'description' => 'nullable|string',
                'duration_minutes' => 'required|integer|min:1',
                'passing_score' => 'required|integer|min:1|max:100',
            ]);

            // Save Step 1 data to session
            $examData['title'] = $request->title;
            $examData['subject_id'] = $request->subject_id;
            $examData['description'] = $request->description;
            $examData['duration_minutes'] = $request->duration_minutes;
            $examData['passing_score'] = $request->passing_score;
            session(['exam_data' => $examData]);

            return redirect()->route('exams.create', ['step' => 2]);
        }

        // Processing STEP 2 -> Going to Step 3
        if ($currentStep == 2) {
            $examData['selected_questions'] = $request->input('questions', []);
            
            // Get new questions and remove any that the user left completely blank
            $examData['new_questions'] = array_filter($request->input('new_questions', []), function($value) {
                return !is_null($value) && trim($value) !== '';
            });

            if (empty($examData['selected_questions']) && empty($examData['new_questions'])) {
                return back()->withErrors(['questions' => 'You must select at least one question or type a new one.']);
            }

            session(['exam_data' => $examData]);
            return redirect()->route('exams.create', ['step' => 3]);
        }

        if ($currentStep == 3) {
            $request->validate([
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $teacher = (object) Session::get('teacher');
            $status = $request->has('publish_immediately') ? 'Published' : 'Draft';
            $accessCode = strtoupper(Str::random(6));

            // 1. Save Exam
            $examId = DB::table('exam_table')->insertGetId([
                'teacher_id' => $teacher->user_id,
                'subject_id' => $examData['subject_id'],
                'Title' => $examData['title'],
                'description' => $examData['description'],
                'duration_minutes' => $examData['duration_minutes'],
                'status' => $status,
                'access_code' => $accessCode,
            ]);

            // 2. Attach Existing Questions
            if (!empty($examData['selected_questions'])) {
                $examQuestions = [];
                foreach ($examData['selected_questions'] as $qId) {
                    $examQuestions[] = ['exam_id' => $examId, 'question_id' => $qId];
                }
                DB::table('exam_question_table')->insert($examQuestions);
            }

            // 3. Create & Attach New Questions
            if (!empty($examData['new_questions'])) {
                foreach ($examData['new_questions'] as $newQ) {
                    $newQId = DB::table('question_table')->insertGetId([
                        'subject_id' => $examData['subject_id'],
                        'quest_desc' => $newQ,
                        'quest_pts' => 1
                    ]);
                    DB::table('exam_question_table')->insert([
                        'exam_id' => $examId,
                        'question_id' => $newQId
                    ]);
                }
            }

            // Clean up the session so the next exam starts fresh
            session()->forget('exam_data'); 

            return redirect()->route('teacherAuth.dashboard')->with('success', "Exam created successfully! Access Code: $accessCode");
        }
    }
}