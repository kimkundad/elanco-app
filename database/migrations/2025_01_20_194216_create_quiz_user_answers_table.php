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
        Schema::create('quiz_user_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // เชื่อมกับ users
            $table->unsignedBigInteger('quiz_id'); // เชื่อมกับ quizzes
            $table->unsignedBigInteger('question_id'); // เชื่อมกับ questions
            $table->unsignedBigInteger('answer_id')->nullable(); // คำตอบของ user (nullable)
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->foreign('answer_id')->references('id')->on('answers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_user_answers');
    }
};
