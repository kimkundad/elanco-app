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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ผู้ทำแบบทดสอบ
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade'); // แบบทดสอบ
            $table->integer('score')->default(0); // คะแนนที่ทำได้
            $table->integer('total_questions')->default(0); // จำนวนคำถามทั้งหมด
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
