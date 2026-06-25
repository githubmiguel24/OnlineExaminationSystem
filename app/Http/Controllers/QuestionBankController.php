<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionBankController extends Controller
{
    public function dashboard()
    {
        $totalSubjects = DB::table('subjects_table')->count();
        $totalQuestions = DB::table('questions_table')->count();
        $totalStudents = DB::table('student_exam_table')->distinct()->count('student_id');

        return view('teacher-auth.dashboard', compact('totalSubjects', 'totalQuestions', 'totalStudents'));
    }

    public function create(Request $request)
    {
        $subjects = DB::table('subjects_table')->get();
        $selectedSubjectId = $request->input('subject_filter');

        $query = DB::table('questions_table as q')
            ->leftJoin('subjects_table as s', 'q.subject_id', '=', 's.subject_id')
            ->select(
                'q.question_id',
                'q.subject_id',
                'q.quest_desc',
                'q.quest_choices',
                'q.quest_answer',
                's.subject_displayname as subject_name'
            );

        if ($selectedSubjectId) {
            $query->where('q.subject_id', '=', $selectedSubjectId);
        }

        $questions = $query->get();

        $questions->transform(function($q) {
            $q->choices = json_decode($q->quest_choices, true) ?? ['A'=>'', 'B'=>'', 'C'=>'', 'D'=>''];
            return $q;
        });

        $currentSubject = $selectedSubjectId 
            ? DB::table('subjects_table')->where('subject_id', $selectedSubjectId)->first() 
            : null;

        return view('teacher-auth.questions', compact('questions', 'subjects', 'currentSubject'));
    }

    // STORE A NEW QUESTION
    public function store(Request $request)
    {
        $request->validate([
            'subject_id'     => ['required'],
            'question_text'  => ['required'],
            'option_a'       => ['required'],
            'option_b'       => ['required'],
            'option_c'       => ['required'],
            'option_d'       => ['required'],
            'correct_option' => ['required']
        ]);

        $choicesJson = json_encode([
            'A' => $request->option_a,
            'B' => $request->option_b,
            'C' => $request->option_c,
            'D' => $request->option_d,
        ]);

        DB::table('questions_table')->insert([
            'subject_id'    => $request->subject_id,
            'quest_desc'    => $request->question_text,
            'quest_choices' => $choicesJson,
            'quest_answer'  => $request->correct_option,
            'quest_pts'     => 1,
        ]);

        return redirect()->route('questions.create', ['subject_filter' => $request->subject_id])
            ->with('success', 'Question added to repository.');
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);

        DB::table('subjects_table')->insert([
            'subject_name'        => strtolower(str_replace(' ', '_', $request->name)),
            'subject_displayname' => $request->name,
            'description'         => '',
        ]);

        return redirect()->route('questions.create')
            ->with('success', 'Subject created successfully.');
    }

    // UPDATE AN EXISTING SUBJECT
    public function updateSubject(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);

        DB::table('subjects_table')
            ->where('subject_id', $id)
            ->update([
                'subject_name'        => strtolower(str_replace(' ', '_', $request->name)),
                'subject_displayname' => $request->name,
            ]);

        return redirect()->route('questions.create', ['subject_filter' => $id])
            ->with('success', 'Subject updated successfully!');
    }

    // DELETE A SUBJECT AND ITS DEPENDENT QUESTIONS
    public function destroySubject($id)
    {
        DB::table('questions_table')->where('subject_id', $id)->delete();
        DB::table('subjects_table')->where('subject_id', $id)->delete();

        return redirect()->route('questions.create')
            ->with('success', 'Subject and its questions removed completely.');
    }

    // DELETE A SINGLE QUESTION
    public function destroy($id)
    {
        $question = DB::table('questions_table')->where('question_id', $id)->first();
        $subjectId = $question ? $question->subject_id : null;

        DB::table('questions_table')->where('question_id', $id)->delete();

        return redirect()->route('questions.create', ['subject_filter' => $subjectId])
            ->with('success', 'Question removed.');
    }
}