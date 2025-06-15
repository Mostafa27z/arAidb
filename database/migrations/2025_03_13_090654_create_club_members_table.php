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
    // Check if referenced tables exist and have correct structure
    if (!Schema::hasTable('clubs') || !Schema::hasTable('students')) {
        throw new RuntimeException('Required tables (clubs or students) do not exist');
    }

    // Verify the id columns in both tables are unsigned big integers
    $clubIdType = Schema::getColumnType('clubs', 'id');
    $studentIdType = Schema::getColumnType('students', 'id');

    if ($clubIdType !== 'bigint' || $studentIdType !== 'bigint') {
        throw new RuntimeException('Referenced columns must be bigint unsigned');
    }

    Schema::create('club_members', function (Blueprint $table) {
        $table->id();
        
        // Explicitly define as unsigned big integer
        $table->unsignedBigInteger('student_id');
        $table->unsignedBigInteger('club_id');
        
        $table->timestamps();
        
        // Add foreign key constraints with explicit references
        $table->foreign('student_id')
            ->references('id')
            ->on('students')
            ->onDelete('cascade');
            
        $table->foreign('club_id')
            ->references('id')
            ->on('clubs')
            ->onDelete('cascade');
            
        // Add composite unique constraint
        $table->unique(['student_id', 'club_id'], 'club_member_unique');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_members');
    }
};
