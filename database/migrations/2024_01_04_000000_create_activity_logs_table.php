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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('action', 100); // upload, download, delete, access_denied, etc.
            $table->text('description')->nullable();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('file_id');
            $table->index('action');
            $table->index('created_at');
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
