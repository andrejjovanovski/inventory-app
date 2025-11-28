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
        Schema::create("members", function (Blueprint $table) {
            $table->id();
            $table->string('badge_number', 3)->unique();
            $table->string("full_name");
            $table->string("national_id")->unique()->nullable();
            $table->date("nid_expiration_date")->nullable();
            $table->string("embg")->unique()->nullable();
            $table->string("passport_number")->unique()->nullable();
            $table->date("passport_expiration_date")->nullable();
            $table->date("date_of_birth")->nullable();
            $table->string("gender")->nullable();
            $table->string("parent_name")->nullable();
            $table->string("parent_email")->nullable();
            $table->string("parent_phone")->nullable();
            $table->string("parent_embg")->nullable();
            $table->string("address")->nullable();
            $table->string("phone_number")->nullable();
            $table->string("email")->unique();
            $table->boolean("is_email_verified")->default(0);
            $table->boolean("is_active")->default(1);
            $table->foreignId("created_by")->constrained("users");
            $table->json("documents")->nullable();
            $table->string("image_path")->nullable();
            $table->date("joining_date")->nullable();
            $table->text("notes")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("members");
    }
};
