<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Course $course, Request $request)
    {
        $course->load(['prerequisites', 'requiredForCourses']);

        $idsParam = $request->query('ids');
        $selectedParam = $request->query('selected', $idsParam);

        $selectedIds = $idsParam
            ? array_values(array_unique(array_filter(array_map('intval', explode(',', $idsParam)))))
            : [];

        $otherIds = array_values(array_diff($selectedIds, [$course->id]));
        $otherCourses = $otherIds
            ? Course::whereIn('id', $otherIds)->get()->sortBy(fn($c) => array_search($c->id, $otherIds))->values()
            : collect();

        $carousel = collect([[
            'id' => $course->id,
            'title' => $course->name,
            'code' => $course->code,
            'color' => $course->color ?: '#0b7af1',
        ]])->concat($otherCourses->map(fn($c) => [
            'id' => $c->id,
            'title' => $c->name,
            'code' => $c->code,
            'color' => $c->color ?: '#0b7af1',
        ]));

        $payload = [
            'course' => [
                'title' => $course->name,
                'code' => $course->code,
                'description' => $course->description,
                'color' => $course->color ?: '#0b7af1',
            ],
            'unlocks' => $course->requiredForCourses->map(fn($c) => [
                'id' => $c->id,
                'color' => $c->color ?: '#64748b',
                'title' => $c->name,
                'code' => $c->code,
            ]),
            'prerequisites' => $course->prerequisites->map(fn($c) => [
                'id' => $c->id,
                'color' => $c->color ?: '#64748b',
                'title' => $c->name,
                'code' => $c->code,
            ]),
            'carousel' => $carousel,
            'idsParam' => $idsParam,
            'selectedParam' => $selectedParam,
        ];

        if ($request->wantsJson()) {
            return response()->json($payload)
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
                ->header('Vary', 'Accept');
        }

        return view('courses.show', $payload);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }
}
