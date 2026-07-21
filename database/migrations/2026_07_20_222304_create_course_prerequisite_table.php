<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_prerequisite', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('prerequisite_id')->constrained('courses')->onDelete('cascade');

            // Enforce unique pairs and optimize indexing
            $table->primary(['course_id', 'prerequisite_id']);
        });

        // Prevent a course from requiring itself
        DB::statement('ALTER TABLE course_prerequisite ADD CONSTRAINT chk_prevent_self_loop CHECK (course_id <> prerequisite_id);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_prerequisite');
    }
};
