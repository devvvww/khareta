<?php

use App\Models\Department;
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
        Department::where('id', 14)->update(['is_general' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Department::where('id', 14)->update(['is_general' => false]);
    }
};
