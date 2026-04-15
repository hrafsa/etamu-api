<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nomor_pengajuan')->unique();
            $table->string('nama_instansi');
            $table->string('atas_nama');
            $table->unsignedInteger('jumlah_peserta');
            $table->string('phone', 30);
            $table->string('email');
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('sub_category_id')->constrained('sub_categories')->cascadeOnDelete();
            $table->date('tanggal_kunjungan');
            $table->time('waktu_kunjungan');
            $table->text('tujuan');
            $table->string('dokumen_path');
            $table->enum('status', ['pending','disetujui','ditolak'])->default('pending');
            $table->timestamps();
            $table->index(['category_id','sub_category_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
