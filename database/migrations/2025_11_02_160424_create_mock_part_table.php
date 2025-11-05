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
        Schema::create('mock_part', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('mock_id')->constrained('mocks')->onDelete('cascade');
            $table->foreignId('part_id')->constrained('parts')->onDelete('cascade');

            // Vaqt ustunlari
            $table->integer('waiting_time')->nullable(); // Kutish vaqti
            $table->integer('timer')->nullable(); // Asosiy taymer

            // Qo'shimcha ma'lumotlar
            $table->string('title')->nullable();
            $table->text('text')->nullable();
            $table->string('audio')->nullable(); // Audio fayl yo'li
            $table->string('photo')->nullable(); // Rasm fayl yo'li

            $table->timestamps();
            $table->softDeletes();

            // Unique constraint - bir mock bir part bilan faqat bir marta bog'lanishi mumkin
            $table->unique(['mock_id', 'part_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mock_part');
    }
};
