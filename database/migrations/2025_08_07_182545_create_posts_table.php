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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title',255)->index(); // Add index for better search performance
            $table->text('content');
            $table->morphs('author'); // This will create author_id and author_type columns
            $table->enum('status', array_column(\App\Enums\PostStatus::cases(), 'value'))
                ->default('draft')->index(); // Add index for filtering by status
            $table->timestamps();

            // Add composite index for common queries
            $table->fullText(['content','title']);
            $table->index(['author_id', 'author_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
