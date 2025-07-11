<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kursis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_id')->nullable();
            $table->string('nomor_kursi');
            $table->enum('status', ['Tersedia', 'Dipesan'])->default('Tersedia');
            $table->timestamps();
            $table->foreign('jadwal_id')
                    ->references('id')
                    ->on('jadwals')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kursis');
    }
};
