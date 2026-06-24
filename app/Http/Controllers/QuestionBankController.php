<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionBankController extends Controller
{
    // Show the Create Question Form
    public function create()
    {
        // Fetch subjects so the teacher can assign the question to a subject
        $subjects = DB::table('subjects_table')->get();
        
        return view('teacher-auth.create-question', compact('subjects'));
    }

    // Process the Form Submission
    public function store(Request $request)
    {
        // 1. Validate the incoming data
        $request->validate([
            'subject_id' => 'required|integer',
            'question_text' => 'required|string',
            'choice_a' => 'required|string',
            'choice_b' => 'required|string',
            'choice_c' => 'required|string',
            'choice_d' => 'required|string',
            'correct_answer' => 'required|string|in:A,B,C,D',
        ]);

        // 2. Package the choices into a simple JSON array to store in the DB easily
        $choices = [
            'A' => $request->choice_a,
            'B' => $request->choice_b,
            'C' => $request->choice_c,
            'D' => $request->choice_d,
        ];

        // 3. Insert into the database
        DB::table('question_table')->insert([
            'subject_id' => $request->subject_id,
            'quest_desc' => $request->question_text,
            'quest_choices' => json_encode($choices), // Saves as a JSON string
            'quest_answer' => $request->correct_answer,
            'quest_pts' => 1
        ]);

        // 4. Check which button the user clicked using pure PHP
        if ($request->submit_action === 'add_another') {
            // Redirect back to the same empty form with a success message
            return back()->with('success', 'Question saved! You can add another one below.');
        }

        // If they clicked "Save and Finish", send them to the dashboard or exam creation page
        return redirect()->route('teacherAuth.dashboard')->with('success', 'Question saved successfully!');
    }
}