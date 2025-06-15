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
       Schema::create('exam_results', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')
        ->constrained('students')
        ->cascadeOnDelete(); // Delete results if student is deleted
    $table->foreignId('exam_id')
        ->constrained('exams')
        ->cascadeOnDelete(); // Delete results if exam is deleted
    $table->float('score');
    $table->float('total_marks');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
