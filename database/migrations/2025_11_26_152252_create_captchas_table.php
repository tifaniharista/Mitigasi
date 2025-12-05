<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('captchas', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // Kode unik CAPTCHA
            $table->text('image_data')->nullable(); // Untuk menyimpan data gambar (opsional)
            $table->string('session_id')->nullable(); // Session ID untuk tracking
            $table->boolean('is_used')->default(false); // Status apakah sudah digunakan
            $table->timestamp('expires_at'); // Waktu kedaluwarsa
            $table->timestamps();

            $table->index(['session_id', 'is_used']);
            $table->index('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('captchas');
    }
};
