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
        Schema::create('group_sessions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
    $table->string('title');
    $table->text('description')->nullable();
    $table->dateTime('session_time');
    $table->string('link'); // رابط Zoom أو Google Meet أو أي منصة
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_sessions');
    }
};
