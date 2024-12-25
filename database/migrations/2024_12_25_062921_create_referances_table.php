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
        Schema::create('referances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id'); // คอลัมน์สำหรับเชื่อมกับ courses
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->string('file')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referances');
    }
};
