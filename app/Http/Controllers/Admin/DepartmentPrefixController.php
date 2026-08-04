<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDepartmentPrefixRequest;
use App\Models\Department;
use App\Models\DepartmentPrefix;
use Illuminate\Http\Request;

class DepartmentPrefixController extends Controller
{
    public function store(StoreDepartmentPrefixRequest $request, Department $department)
    {
        $data = $request->validated();

        $prefix = $department->prefixes()->create([
            'prefix' => strtoupper($data['prefix']),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['id' => $prefix->id, 'prefix' => $prefix->prefix]);
        }

        return back()->with('status', 'تمت إضافة الرمز بنجاح');
    }

    public function destroy(DepartmentPrefix $prefix, Request $request)
    {
        if ($prefix->courses()->exists()) {
            $message = 'لا يمكن حذف هذا الرمز لأنه مستخدم في مواد حالية';

            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $prefix->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'تم الحذف']);
        }

        return back()->with('status', 'تم حذف الرمز');
    }
}
