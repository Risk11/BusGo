<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bus_id')->nullable();
            $table->date('tanggal_berangkat')->nullable();
            $table->time('jam_berangkat')->nullable();
            $table->string('pol_keberangkatan')->nullable();
            $table->string('tujuan')->nullable();
            $table->decimal('harga', 10, 2)->nullable();
            $table->foreign('bus_id')
                    ->references('id')
                    ->on('bus')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
