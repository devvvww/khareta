<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseManageRequest;
use App\Http\Requests\Admin\UpdateCourseManageRequest;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;

class CourseManageController extends Controller
{
    public function index(Department $department, Request $request)
    {
        $courses = $department->courses()
            ->with('requiredForCourses:id,name')
            ->when($request->filled('elective'), function ($q) use ($request) {
                $q->where('is_elective', $request->elective === '1');
            })
            ->orderBy('name')
            ->get();

        return view('admin.courses.index', compact('department', 'courses'));
    }

    public function create(Department $department)
    {
        $allCourses = $this->prerequisiteCandidates($department);

        return view('admin.courses.form', [
            'department' => $department,
            'course' => new Course(),
            'allCourses' => $allCourses,
            'selectedPrerequisiteIds' => [],
            'codeNumber' => '',
            'disabledPrerequisiteIds' => [],
        ]);
    }

    public function store(StoreCourseManageRequest $request, Department $department)
    {
        $data = $request->validated();
        $prefix = $department->prefixes()->findOrFail($data['department_prefix_id']);

        $data['is_elective'] = $department->allows_electives ? $request->boolean('is_elective') : false;
        $data['department_id'] = $department->id;
        $data['color'] = $data['is_elective'] ? '#10b981' : $department->color;
        $data['code'] = $prefix->prefix . $data['code_number'];
        unset($data['code_number']);

        $course = Course::create($data);
        $course->prerequisites()->sync($request->input('prerequisites', []));

        return redirect()->route('admin.departments.courses.index', $department)->with('status', 'تمت إضافة المادة بنجاح');
    }

    public function edit(Course $course)
    {
        $department = $course->department;
        $allCourses = $this->prerequisiteCandidates($department, excludeCourseId: $course->id);
        $selectedPrerequisiteIds = $course->prerequisites->pluck('id')->toArray();

        $codeNumber = $course->departmentPrefix
            ? substr($course->code, strlen($course->departmentPrefix->prefix))
            : $course->code;

        $disabledPrerequisiteIds = $course->requiredForCourses->pluck('id')->toArray();

        return view('admin.courses.form', compact('department', 'course', 'allCourses', 'selectedPrerequisiteIds', 'codeNumber', 'disabledPrerequisiteIds'));
    }

    public function update(UpdateCourseManageRequest $request, Course $course)
    {
        $data = $request->validated();
        $prefix = $course->department->prefixes()->findOrFail($data['department_prefix_id']);

        $data['is_elective'] = $course->department->allows_electives ? $request->boolean('is_elective') : false;
        $data['color'] = $data['is_elective'] ? '#10b981' : $course->department->color;
        $data['code'] = $prefix->prefix . $data['code_number'];
        unset($data['code_number']);

        $course->update($data);
        $course->prerequisites()->sync($request->input('prerequisites', []));

        return redirect()
            ->route('admin.departments.courses.index', $course->department_id)
            ->with('status', 'تم تحديث المادة بنجاح');
    }
    
    public function destroy(Course $course)
    {
        $departmentId = $course->department_id;
        $course->delete();

        return redirect()
            ->route('admin.departments.courses.index', $departmentId)
            ->with('status', 'تم حذف المادة');
    }

    private function prerequisiteCandidates(Department $department, ?int $excludeCourseId = null)
    {
        $departmentIds = [$department->id];

        if (!$department->is_general) {
            $generalDepartment = Department::where('is_general', true)->first();
            if ($generalDepartment) {
                $departmentIds[] = $generalDepartment->id;
            }
        }

        return Course::whereIn('department_id', $departmentIds)
            ->when($excludeCourseId, fn($q) => $q->where('id', '!=', $excludeCourseId))
            ->orderBy('name')
            ->get();
    }
}
