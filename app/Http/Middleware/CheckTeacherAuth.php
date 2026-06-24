<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTeacherAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the 'teacher' session exists
        if (!session()->has('teacher')) {
            // Redirect to the teacher login route, not the student one
            return redirect()->route('teacherAuth.login')->with('error', 'Please log in as a teacher first.');
        }
        
        return $next($request);
    }
}