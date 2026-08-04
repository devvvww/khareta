<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDepartmentManageRequest;
use App\Http\Requests\Admin\UpdateDepartmentManageRequest;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentManageController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('courses')->orderBy('name')->get();

        return view('admin.departments.index', compact('departments'));
    }
    public function create()
    {
        return view('admin.departments.form', [
            'department' => new Department(),
            'primaryPrefix' => null,
            'additionalPrefixes' => collect(),
        ]);
    }

    public function store(StoreDepartmentManageRequest $request)
    {
        $data = $request->validated();
        $data['allows_electives'] = $request->boolean('allows_electives');

        $primaryPrefix = strtoupper($data['prefix']);
        $extraPrefixes = array_map('strtoupper', $data['prefixes'] ?? []);
        unset($data['prefix'], $data['prefixes']);

        $department = Department::create($data);
        $department->prefixes()->create(['prefix' => $primaryPrefix]);

        foreach ($extraPrefixes as $extra) {
            $department->prefixes()->create(['prefix' => $extra]);
        }

        return redirect()->route('admin.departments.index')->with('status', 'تمت إضافة القسم بنجاح');
    }

    public function edit(Department $department)
    {
        $primaryPrefix = $department->prefixes()->oldest()->first();
        $additionalPrefixes = $department->prefixes()->where('id', '!=', $primaryPrefix?->id)->get();

        return view('admin.departments.form', compact('department', 'primaryPrefix', 'additionalPrefixes'));
    }

    public function update(UpdateDepartmentManageRequest $request, Department $department)
    {
        $data = $request->validated();
        $data['allows_electives'] = $request->boolean('allows_electives');

        $primaryPrefixValue = strtoupper($data['primary_prefix']);
        unset($data['primary_prefix']);

        $department->update($data);

        $primaryPrefix = $department->prefixes()->oldest()->first();
        if ($primaryPrefix) {
            $primaryPrefix->update(['prefix' => $primaryPrefixValue]);
        } else {
            $department->prefixes()->create(['prefix' => $primaryPrefixValue]);
        }

        return redirect()->route('admin.departments.index')->with('status', 'تم تحديث القسم بنجاح');
    }
    
    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('admin.departments.index')->with('status', 'تم حذف القسم');
    }
}
