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
        // $course = [
        //     'title' => 'مقدمة في قواعد البيانات',
        //     'code' => 'ITCS214',
        //     'description' => 'الأساس المتين لفهم كيفية تخزين وإدارة البيانات بكفاءة عالية.',
        // ];

        // $unlocks = [
        //     [
        //         'variant' => 'red',
        //         'title' => 'برمجة ويب متقدمة',
        //         'code' => 'ITWS310',
        //         'description' => 'بناء تطبيقات ويب متكاملة.',
        //     ],
        //     [
        //         'variant' => 'green',
        //         'title' => 'تنقيب البيانات',
        //         'code' => 'ITDS320',
        //         'description' => 'تحليل البيانات الضخمة.',
        //     ],
        //     [
        //         'variant' => 'green',
        //         'title' => 'برمجة الإنترنت المتقدمة',
        //         'code' => 'ITSE414',
        //         'description' => 'تحليل البيانات الضخمة.',
        //     ],
        // ];

        // $prerequisites = [
        //     [
        //         'variant' => 'blue',
        //         'title' => 'برمجة 1',
        //         'code' => 'ITCS101',
        //         'description' => 'أساسيات البرمجة بلغة C++.',
        //     ],
        //     [
        //         'variant' => 'blue',
        //         'title' => 'تراكيب محددة',
        //         'code' => 'ITMA102',
        //         'description' => 'المنطق والرياضيات المتقطعة.',
        //     ],
        //     [
        //         'variant' => 'blue',
        //         'title' => 'مهارات الحاسب',
        //         'code' => 'ITGS101',
        //         'description' => 'مقدمة في تقنية المعلومات.',
        //     ],
        //     [
        //         'variant' => 'blue',
        //         'title' => 'تراكيب محددة',
        //         'code' => 'ITMA102',
        //         'description' => 'المنطق والرياضيات المتقطعة.',
        //     ],
        //     [
        //         'variant' => 'blue',
        //         'title' => 'مهارات الحاسب',
        //         'code' => 'ITGS101',
        //         'description' => 'مقدمة في تقنية المعلومات.',
        //     ],
        // ];


        // return view('courses.show', compact('course', 'unlocks', 'prerequisites'));
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
