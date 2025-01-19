<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('home_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->enum('status', ['public', 'private'])->default('public');
            $table->integer('order')->default(1);
            $table->string('desktop_image')->nullable();
            $table->string('mobile_image')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_banners');
    }
};
