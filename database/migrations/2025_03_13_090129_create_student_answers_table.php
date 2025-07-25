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
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id(); // Standard Laravel 'id' instead of 'answer_id'
             $table->foreignId('student_id')
        ->constrained('students')
        ->cascadeOnDelete(); // Delete answers when student is deleted
    $table->foreignId('question_id')
        ->constrained('questions')
        ->cascadeOnDelete(); // Delete answers when question is deleted
    $table->foreignId('selected_option_id')
        ->nullable()
        ->constrained('question_options')
        ->nullOnDelete();
            $table->text('essay_answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->timestamps(); // Using timestamps() instead of just answered_at
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};
