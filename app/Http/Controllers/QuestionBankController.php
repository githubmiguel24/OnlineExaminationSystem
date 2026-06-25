<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionBankController extends Controller
{
    // Renders the main dashboard metrics screen
    public function dashboard()
    {
        $totalSubjects = DB::table('subjects_table')->count();
        $totalQuestions = DB::table('questions_table')->count();
        $totalStudents = DB::table('student_exam_table')->distinct()->count('student_id');

        return view('teacher-auth.dashboard', compact('totalSubjects', 'totalQuestions', 'totalStudents'));
    }

    // Displays the Question & Subject Management Panel on its own webpage
    public function create(Request $request)
{
    $subjects = DB::table('subjects_table')->get();
    $selectedSubjectId = $request->input('subject_filter');

    $query = DB::table('questions_table as q')
        ->leftJoin('subjects_table as s', 'q.subject_id', '=', 's.subject_id')
        ->select(
            'q.question_id',
            'q.subject_id',
            'q.quest_desc',     // Changed to match your database field
            'q.quest_choices',  // Changed to match your database field
            'q.quest_answer',   // Changed to match your database field
            's.subject_displayname as subject_name'
        );

    if ($selectedSubjectId) {
        $query->where('q.subject_id', '=', $selectedSubjectId);
    }

    $questions = $query->get();

    // Map through questions to decode the JSON text choices safely for Blade
    $questions->transform(function($q) {
        $q->choices = json_decode($q->quest_choices, true) ?? ['A'=>'', 'B'=>'', 'C'=>'', 'D'=>''];
        return $q;
    });

    $currentSubject = $selectedSubjectId 
        ? DB::table('subjects_table')->where('subject_id', $selectedSubjectId)->first() 
        : null;

    return view('teacher-auth.questions', compact('questions', 'subjects', 'currentSubject'));
}

public function storeSubject(Request $request)
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

    // Encode the choices array into a JSON string to match your database 'quest_choices'
    $choicesJson = json_encode([
        'A' => $request->option_a,
        'B' => $request->option_b,
        'C' => $request->option_c,
        'D' => $request->option_d,
    ]);

    // Force the correct database column mappings based on your actual schema
    DB::table('questions_table')->insert([
        'subject_id'    => $request->subject_id,
        'quest_desc'    => $request->question_text, // Maps form input to quest_desc
        'quest_choices' => $choicesJson,          // Maps options to quest_choices
        'quest_answer'  => $request->correct_option, // Maps answer key to quest_answer
        'quest_pts'     => 1                         // Default point value
    ]);

    return redirect()->route('questions.create', ['subject_filter' => $request->subject_id])
        ->with('success', 'Question added to repository.');
}

    // Edits/Updates an existing subject
    public function updateSubject(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);

        // Removed 'updated_at' => now() as well
        DB::table('subjects_table')
            ->where('subject_id', $id)
            ->update([
                'subject_name'        => strtolower(str_replace(' ', '_', $request->name)),
                'subject_displayname' => $request->name,
            ]);

        return redirect()->route('questions.create', ['subject_filter' => $id])
            ->with('success', 'Subject updated successfully!');
    }

    // Deletes a subject and its corresponding questions
    public function destroySubject($id)
    {
        // Delete all dependent questions first to preserve database integrity
        DB::table('questions_table')->where('subject_id', $id)->delete();
        
        // Delete the parent subject record
        DB::table('subjects_table')->where('subject_id', $id)->delete();

        // Redirects back to the dedicated question bank track cleared out
        return redirect()->route('questions.create')
            ->with('success', 'Subject and its associated question bank removed completely.');
    }

    // Inserts a new question linked to an active subject workspace
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

        DB::table('questions_table')->insert([
            'subject_id'     => $request->subject_id,
            'question_text'  => $request->question_text,
            'option_a'       => $request->option_a,
            'option_b'       => $request->option_b,
            'option_c'       => $request->option_c,
            'option_d'       => $request->option_d,
            'correct_option' => $request->correct_option,
            'created_at'     => now()
        ]);

        // Redirects back keeping the active subject filtered in the interface workspace
        return redirect()->route('question.create', ['subject_filter' => $request->subject_id])
            ->with('success', 'Question added to repository.');
    }

    // Removes a specific question
    public function destroy(int $id)
    {
        // Track the subject before deletion to keep the browser workspace pinned
        $question = DB::table('questions_table')->where('id', $id)->first();
        $subjectId = $question ? $question->subject_id : null;

        DB::table('questions_table')->where('id', $id)->delete();

        // Redirects back keeping the active subject workspace focused
        return redirect()->route('question.create', ['subject_filter' => $subjectId])
            ->with('success', 'Question removed.');
    }
}