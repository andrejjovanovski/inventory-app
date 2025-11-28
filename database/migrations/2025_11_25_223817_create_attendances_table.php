<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("attendances", function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->date("attendance_date");
            $table->time("start_time");
            $table->time("end_time");
            $table->foreignId("mentor_id")->constrained("users");
            $table->foreignId("group_id")->constrained("groups");
            $table->foreignId("created_by")->constrained("users");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("attendances");
    }
};
