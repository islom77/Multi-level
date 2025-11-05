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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_type_id')->constrained('question_types')->onDelete('cascade');
            $table->string('name');
            $table->text('text')->nullable(); // Savol uchun qo'shimcha matn
            $table->integer('order')->nullable(); // Mock ichidagi savol raqami (21, 22, 23...)
            $table->unsignedBigInteger('parent_id')->nullable(); // ota_id = parent_id
            $table->unsignedBigInteger('true_option_id')->nullable(); // Matching savollar uchun (FK alohida migration'da)
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
