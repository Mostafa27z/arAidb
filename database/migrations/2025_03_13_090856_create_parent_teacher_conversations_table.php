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
        Schema::create('parent_teacher_conversations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('parent_id')
        ->constrained('users')
        ->cascadeOnDelete(); // Delete conversation if parent is deleted
    $table->foreignId('teacher_id')
        ->constrained('teachers')
        ->cascadeOnDelete(); // Delete conversation if teacher is deleted
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_teacher_conversations');
    }
};
