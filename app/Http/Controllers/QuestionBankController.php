<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionBankController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all subjects for the dropdown form selectors
        $subjects = DB::table('subjects')->get();

        // Start our query builder with a leftJoin to pull subject details
        $query = DB::table('questions')
            ->leftJoin('subjects', 'questions.subject_id', '=', 'subjects.id')
            ->select('questions.*', 'subjects.name as subject_name');

        // UX Feature: If a subject filter is picked from the dropdown, narrow down results
        if ($request->has('subject_filter') && $request->subject_filter != '') {
            $query->where('questions.subject_id', '=', $request->subject_filter);
        }

        $questions = $query->get();

        return view('questions.index', compact('questions', 'subjects'));
    }

    public function store(Request $request)
    {
        // 1. Validation rule configuration (Notice: standard arrow notation, no callable parentheses)
        $request->validate([
            'subject_id'     => ['required'],
            'question_text'  => ['required'],
            'option_a'       => ['required'],
            'option_b'       => ['required'],
            'option_c'       => ['required'],
            'option_d'       => ['required'],
            'correct_option' => ['required']
        ]);

        // 2. Insert into the database
        DB::table('questions')->insert([
            'subject_id'     => $request->subject_id,
            'question_text'  => $request->question_text,
            'option_a'       => $request->option_a,
            'option_b'       => $request->option_b,
            'option_c'       => $request->option_c,
            'option_d'       => $request->option_d,
            'correct_option' => $request->correct_option,
            'created_at'     => now()
        ]);

        return redirect()->route('questions.index')->with('success', 'Question added to the bank successfully!');
    }

    public function destroy(int $id)
    {
        DB::table('questions')->where('id', $id)->delete();
        return redirect()->route('questions.index')->with('success', 'Question deleted.');
    }
}