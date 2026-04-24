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
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('resume_text');
            $table->dropColumn('cover_letter_text');
            $table->dropColumn('has_missing_skills');
            $table->dropColumn('status');
            $table->dropColumn('viewed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
