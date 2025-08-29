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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image');
            $table->mediumText('image_caption')->nullable();
            $table->longText('content');
            $table->integer('min_read')->default(0);
            $table->string('teaser')->nullable();

            $table->enum('status', ['draft', 'pending', 'published', 'rejected', 'archived'])->default('pending');
            $table->text('rejected_reason')->nullable();

            $table->text('edit_reason')->nullable();

            $table->timestamp('published_at')->nullable();

            $table->integer('views_count')->default(0);

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            $table->fullText(['title', 'slug']);
            $table->timestamps();
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
