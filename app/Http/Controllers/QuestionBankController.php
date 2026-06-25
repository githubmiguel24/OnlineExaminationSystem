<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionBankController extends Controller
{
    public function create()
    {
        $subjects = DB::table('subjects_table')->get();
        
        return view('teacher-auth.create-question', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|integer',
            'question_text' => 'required|string',
            'choice_a' => 'required|string',
            'choice_b' => 'required|string',
            'choice_c' => 'required|string',
            'choice_d' => 'required|string',
            'correct_answer' => 'required|string|in:A,B,C,D',
        ]);

        $choices = [
            'A' => $request->choice_a,
            'B' => $request->choice_b,
            'C' => $request->choice_c,
            'D' => $request->choice_d,
        ];

        DB::table('questions_table')->insert([
            'subject_id' => $request->subject_id,
            'quest_desc' => $request->question_text,
            'quest_choices' => json_encode($choices), // Saves as a JSON string
            'quest_answer' => $request->correct_answer,
            'quest_pts' => 1
        ]);

        if ($request->submit_action === 'add_another') {
            return back()->with('success', 'Question saved! You can add another one below.');
        }

        return redirect()->route('teacherAuth.dashboard')->with('success', 'Question saved successfully!');
    }
}