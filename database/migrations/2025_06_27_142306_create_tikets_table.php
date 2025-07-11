<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tikets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_booking')->nullable();
            $table->unsignedBigInteger('penumpang_id')->nullable();
            $table->unsignedBigInteger('jadwal_id')->nullable();
            $table->string('nomor_kursi')->nullable();
            $table->timestamp('tanggal_pemesanan')->useCurrent();
            $table->enum('status_pembayaran', ['Belum Bayar', 'Lunas', 'Gagal', 'Kadaluarsa'])->default('Belum Bayar');
            $table->timestamps();

            $table->foreign('penumpang_id')->references('id')->on('penumpangs')->onDelete('cascade');
            $table->foreign('jadwal_id')->references('id')->on('jadwals')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tikets');
    }
};
