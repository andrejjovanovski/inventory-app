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
        Schema::create("attendance_member", function (Blueprint $table) {
            $table->id();
            $table->foreignId("attendance_id")->constrained("attendances");
            $table->foreignId("member_id")->constrained("members");
            $table->boolean("is_present");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("attendance_member");
    }
};
