<?php

use App\Models\Course;
use App\Models\Department;
use App\Models\DepartmentPrefix;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Department::whereNotNull('prefix')->each(function ($department) {
            $prefixRow = DepartmentPrefix::create([
                'department_id' => $department->id,
                'prefix' => $department->prefix,
            ]);

            Course::where('department_id', $department->id)
                ->where('code', 'like', $department->prefix . '%')
                ->update(['department_prefix_id' => $prefixRow->id]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //This data migration is not safely reversible. Restore from a database backup if you need to undo it.
    }
};
