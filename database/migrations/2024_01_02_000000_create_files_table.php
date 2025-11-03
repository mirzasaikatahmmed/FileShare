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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('original_name');
            $table->string('stored_name')->unique();
            $table->string('file_path');
            $table->bigInteger('file_size'); // Size in bytes
            $table->string('mime_type');
            $table->string('extension', 10);
            $table->string('password'); // Hashed password
            $table->string('short_code', 20)->unique()->index();
            $table->string('qr_code_path')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('max_downloads')->nullable();
            $table->integer('downloads_count')->default(0);
            $table->string('ip_address', 45); // Supports IPv4 and IPv6
            $table->text('user_agent')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('created_at');
            $table->index('is_active');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
