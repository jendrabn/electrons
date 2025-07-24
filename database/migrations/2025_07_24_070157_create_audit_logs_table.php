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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type'); // Nama model yang diaudit
            $table->unsignedBigInteger('auditable_id'); // ID record yang diaudit
            $table->string('user_type')->nullable(); // Tipe user (biasanya App\Models\User)
            $table->unsignedBigInteger('user_id')->nullable(); // ID user yang melakukan aksi
            $table->string('action'); // created, updated, deleted
            $table->text('description'); // Deskripsi aksi
            $table->json('old_values')->nullable(); // Data lama
            $table->json('new_values')->nullable(); // Data baru
            $table->ipAddress('ip_address')->nullable(); // IP address
            $table->text('user_agent')->nullable(); // User agent
            $table->timestamps();

            // Indexes untuk performa
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['user_type', 'user_id']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
