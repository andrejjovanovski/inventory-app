<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('school_level')->nullable()->after('gender');
            $table->string('school_name')->nullable()->after('school_level');
            $table->string('ethnicity')->nullable()->after('school_name');

            $table->string('parent_national_id')->nullable()->after('parent_embg');
            $table->date('parent_nid_expiration_date')->nullable()->after('parent_national_id');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'school_level',
                'school_name',
                'ethnicity',
                'parent_national_id',
                'parent_nid_expiration_date',
            ]);
        });
    }
};