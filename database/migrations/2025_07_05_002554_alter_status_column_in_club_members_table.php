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
    DB::statement("ALTER TABLE club_members MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
}

public function down(): void
{
    DB::statement("ALTER TABLE club_members MODIFY COLUMN status ENUM('pending', 'approved') DEFAULT 'pending'");
}

};
