<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Course $course, Request $request)
    {
        $course->load('prerequisites');

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

        // Unlocks are scoped to the student's current department context
        // (plus general education, which stays relevant regardless of major)
        // so a general course only shows the unlocks actually relevant to
        // the path the student is currently on — not every department's
        // independent use of that same general course.
        $contextDepartmentId = session('department_id');

        $unlocksQuery = $course->requiredForCourses();

        if ($contextDepartmentId) {
            $generalDepartmentId = Department::where('is_general', true)->value('id');

            $relevantDepartmentIds = array_unique(array_filter([
                $contextDepartmentId,
                $generalDepartmentId,
            ]));

            $unlocksQuery->whereIn('courses.department_id', $relevantDepartmentIds);
        }

        $unlocks = $unlocksQuery->get();

        $payload = [
            'course' => [
                'title' => $course->name,
                'code' => $course->code,
                'description' => $course->description,
                'color' => $course->color ?: '#0b7af1',
            ],
            'unlocks' => $unlocks->map(fn($c) => [
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
