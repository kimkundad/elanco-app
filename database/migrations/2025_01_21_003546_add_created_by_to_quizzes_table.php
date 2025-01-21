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
        Schema::table('quizzes', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('pass_percentage'); // เพิ่มคอลัมน์ created_by
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null'); // ตั้งค่า Foreign Key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['created_by']); // ลบ Foreign Key
            $table->dropColumn('created_by'); // ลบคอลัมน์
        });
    }
};
