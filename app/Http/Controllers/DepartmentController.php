<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Request;

// Department Selection Controller
class DepartmentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        
        session(['department_id' => $request->department_id]);

        return redirect($request->input('redirect') ?: route('courses.search'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        return view('departments.select', [
            'departments' => Department::orderBy('name')->get(),
            'redirect' => $request->query('redirect'),
        ]);
    }
}
