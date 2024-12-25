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
        Schema::create('course_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id'); // เชื่อมโยงกับ courses
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ผู้ทำแบบทดสอบ
            $table->boolean('isFinishCourse')->default(false);
            $table->integer('lastTimestamp')->default(0); // เวลาหน่วยเป็นวินาที
            $table->boolean('isFinishVideo')->default(false);
            $table->boolean('isFinishQuiz')->default(false);
            $table->boolean('isDownloadCertificate')->default(false);
            $table->boolean('isReview')->default(false);
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_actions');
    }
};
