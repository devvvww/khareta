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
            : [$course->id];

        // Make sure the current course is always included, even if it somehow
        // wasn't in the ids list (e.g. someone edited the URL manually).
        if (!in_array($course->id, $selectedIds)) {
            array_unshift($selectedIds, $course->id);
        }

        $allCarouselCourses = Course::whereIn('id', $selectedIds)
            ->get()
            ->sortBy(fn($c) => array_search($c->id, $selectedIds))
            ->values();

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

            $unlocksResult = $unlocksQuery->get();
            $unlocksResult = $this->sortUnlocksByDepartmentThenGeneral($unlocksResult, $contextDepartmentId); // ← the call

            $prefetchedData[$c->id] = [
                'title' => $c->name,
                'code' => $c->code,
                'unlocks' => $unlocksResult->map(fn($u) => [
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
            'currentCourseId' => $course->id,
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

    private function sortUnlocksByDepartmentThenGeneral($unlocks, $contextDepartmentId)
    {
        $contextDepartmentId = (int) $contextDepartmentId;

        return $unlocks->sortBy(function ($u) use ($contextDepartmentId) {
            return (int) $u->department_id === $contextDepartmentId ? 0 : 1;
        })->values();
    }
}
