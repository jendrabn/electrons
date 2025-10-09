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

            // Add FULLTEXT index when supported (MySQL/MariaDB). SQLite used in tests
            // does not support fulltext via Schema builder, so guard to avoid runtime
            // errors during test migrations.
            try {
                if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                    $table->fullText(['title', 'slug']);
                }
            } catch (\Throwable $e) {
                // be defensive in case the driver lacks fulltext support
            }
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
