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
        Schema::create('mock_skill_part', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_skill_id')->constrained('mock_skill')->onDelete('cascade');
            $table->foreignId('part_id')->constrained('parts')->onDelete('cascade');
            $table->integer('waiting_time')->nullable();
            $table->integer('timer')->nullable();
            $table->string('title')->nullable();
            $table->text('text')->nullable();
            $table->string('audio')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['mock_skill_id', 'part_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mock_skill_part');
    }
};
