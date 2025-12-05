<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jenis_dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('tahapan_id')->constrained()->onDelete('cascade');
            $table->string('nama_dokumen');
            $table->string('versi')->default('1.0');
            $table->date('tanggal_realisasi')->nullable();
            $table->date('tanggal_revisi')->nullable();
            $table->string('file_dokumen')->nullable();
            $table->string('file_pendukung')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jenis_dokumen');
    }
};
