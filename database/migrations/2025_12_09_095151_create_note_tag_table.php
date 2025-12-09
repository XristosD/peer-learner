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
        Schema::create('note_tag', function (Blueprint $table) {
            $table->ulid('note_id');
            $table->ulid('tag_id');
            $table->ulid('book_id');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->primary(['note_id', 'tag_id']);
            $table->foreign('note_id')->references('id')->on('notes')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();
            $table->foreign('book_id')->references('id')->on('books')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_tag');
    }
};
