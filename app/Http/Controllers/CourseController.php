<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {

        $course->load(['prerequisites', 'requiredForCourses']);

        return view('courses.show', [
            'course' => [
                'title' => $course->name,
                'code' => $course->code,
                'description' => $course->description,
                'color' => $course->color ?: '#0b7af1'
            ],
            'unlocks' => $course->requiredForCourses->map(fn($c) => [
                'color' => $c->color ?: '#64748b',
                'title' => $c->name,
                'code' => $c->code,
            ]),
            'prerequisites' => $course->prerequisites->map(fn($c) => [
                'color' => $c->color ?: '#64748b',
                'title' => $c->name,
                'code' => $c->code,
            ]),
        ]);
            
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
