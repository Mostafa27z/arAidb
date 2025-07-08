<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_exam_answers', function (Blueprint $table) {
            $table->id();

            // العلاقات
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('exam_question_id')->constrained('exam_questions')->onDelete('cascade');
            $table->foreignId('selected_option_id')->nullable()->constrained('exam_question_options')->nullOnDelete();

            // إجابة نصية (للمقالي)
            $table->text('essay_answer')->nullable();

            // درجة التصحيح (للمقالي فقط)
            $table->float('score')->nullable();

            $table->timestamps();

            // التأكد من أن الطالب لا يجاوب نفس السؤال مرتين في نفس الامتحان
            $table->unique(['student_id', 'exam_id', 'exam_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_exam_answers');
    }
};
