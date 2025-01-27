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
        Schema::table('home_banners', function (Blueprint $table) {
            $table->string('link')->nullable()->after('mobile_image'); // เพิ่มคอลัมน์ link
            $table->text('description')->nullable()->after('link'); // เพิ่มคอลัมน์ description
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_banners', function (Blueprint $table) {
            $table->dropColumn('link');
            $table->dropColumn('description');
        });
    }
};
