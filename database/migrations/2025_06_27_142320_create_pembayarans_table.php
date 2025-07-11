<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tiket_id')->nullable();
            $table->string('metode')->nullable();
            $table->decimal('jumlah_bayar', 10, 2)->nullable();
            $table->timestamp('waktu_bayar')->nullable();
            $table->enum('status', ['Sukses', 'Gagal', 'Pending'])->default('Pending');
            $table->string('kode_transaksi_gateway')->nullable();
            $table->timestamps();

            $table->foreign('tiket_id')
                    ->references('id')
                    ->on('tikets')
                    ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
