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
        Schema::create('group_members', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')
        ->constrained('students')
        ->cascadeOnDelete(); // Delete membership when student is deleted
    $table->foreignId('group_id')
        ->constrained('groups')
        ->cascadeOnDelete(); // Delete membership when group is deleted
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
