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
        Schema::table('survey_response_answers', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable()->after('survey_response_id');

            // ตั้ง Foreign Key เชื่อมกับตาราง `courses`
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_response_answers', function (Blueprint $table) {
            // ลบ Foreign Key ก่อน
            $table->dropForeign(['course_id']);

            // ลบคอลัมน์
            $table->dropColumn('course_id');
        });
    }
};
