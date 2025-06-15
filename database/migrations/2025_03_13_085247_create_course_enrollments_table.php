<?php

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
        Schema::create('course_enrollments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')
        ->constrained('students')
        ->cascadeOnDelete(); // Delete enrollment when student is deleted
    $table->foreignId('course_id')
        ->constrained('courses')
        ->cascadeOnDelete(); // Delete enrollment when course is deleted
    $table->timestamp('enrolled_at')->useCurrent();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
