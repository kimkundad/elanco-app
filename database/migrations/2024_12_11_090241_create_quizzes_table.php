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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('quiz_id')->unique(); // รหัส Quiz (เช่น Q001)
            $table->string('expire_date')->nullable(); // วันหมดอายุ
            $table->string('questions_title'); // ชื่อหัวข้อคำถาม
            $table->unsignedInteger('pass_percentage')->default(0); // เปอร์เซ็นต์ผ่าน (เช่น 50, 60)
            $table->boolean('certificate')->default(false); // ได้รับใบรับรอง (Yes/No -> 1/0)
            $table->integer('point_cpd')->nullable(); // คะแนน CPD / CE (จำนวนเต็ม)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
