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
        Schema::create('sub_category_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');         // Foreign Key เชื่อมกับตาราง users
            $table->unsignedBigInteger('sub_category_id'); // Foreign Key เชื่อมกับตาราง sub_categories
            $table->timestamps();

            // เพิ่มความสัมพันธ์ Foreign Key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_category_user');
    }
};
