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
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('survey_id')->unique(); // รหัส Survey (เช่น S001)
            $table->string('survey_title'); // ชื่อ Survey
            $table->text('survey_detail')->nullable(); // รายละเอียด Survey
            $table->date('expire_date')->nullable(); // วันหมดอายุของ Survey
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
