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
        Schema::create('survey_response_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survey_response_id'); // เชื่อมกับ survey_responses
            $table->unsignedBigInteger('survey_question_id'); // เชื่อมกับ survey_questions
            $table->unsignedBigInteger('survey_answer_id')->nullable(); // คำตอบที่เลือก (ถ้ามี)
            $table->text('custom_answer')->nullable(); // คำตอบที่พิมพ์เอง
            $table->timestamps();

            $table->foreign('survey_response_id')->references('id')->on('survey_responses')->onDelete('cascade');
            $table->foreign('survey_question_id')->references('id')->on('survey_questions')->onDelete('cascade');
            $table->foreign('survey_answer_id')->references('id')->on('survey_answers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_response_answers');
    }
};
