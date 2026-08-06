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

        $allCarouselCourses = collect([$course])->concat($otherCourses);

        $carousel = $allCarouselCourses->map(fn($c) => [
            'id' => $c->id,
            'title' => $c->name,
            'code' => $c->code,
            'color' => $c->color ?: '#0b7af1',
        ]);

        $contextDepartmentId = session('department_id');
        $generalDepartmentId = Department::where('is_general', true)->value('id');
        $relevantDepartmentIds = array_unique(array_filter([$contextDepartmentId, $generalDepartmentId]));

        // Pre-fetch unlocks + prerequisites for EVERY course in the carousel,
        // not just the one currently centered — this is what lets swiping
        // render instantly with zero network requests.
        $prefetchedData = [];

        foreach ($allCarouselCourses as $c) {
            $c->loadMissing('prerequisites');

            $unlocksQuery = $c->requiredForCourses();
            if ($contextDepartmentId) {
                $unlocksQuery->whereIn('courses.department_id', $relevantDepartmentIds);
            }

            $prefetchedData[$c->id] = [
                'title' => $c->name,
                'code' => $c->code,
                'unlocks' => $unlocksQuery->get()->map(fn($u) => [
                    'id' => $u->id,
                    'color' => $u->color ?: '#64748b',
                    'title' => $u->name,
                    'code' => $u->code,
                ]),
                'prerequisites' => $c->prerequisites->map(fn($p) => [
                    'id' => $p->id,
                    'color' => $p->color ?: '#64748b',
                    'title' => $p->name,
                    'code' => $p->code,
                ]),
            ];
        }

        $payload = [
            'course' => [
                'title' => $course->name,
                'code' => $course->code,
                'description' => $course->description,
                'color' => $course->color ?: '#0b7af1',
            ],
            'unlocks' => $prefetchedData[$course->id]['unlocks'],
            'prerequisites' => $prefetchedData[$course->id]['prerequisites'],
            'carousel' => $carousel,
            'prefetchedData' => $prefetchedData,
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
