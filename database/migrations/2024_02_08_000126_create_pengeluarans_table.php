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
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kode_pengeluaran')->nullable(false);
            $table->unsignedBigInteger('id_jenis')->nullable(false);
            $table->unsignedBigInteger('id_donatur')->nullable(false);
            $table->integer('jumlah_pemasukan')->nullable(false);
            $table->dateTime('tanggal_pemasukan')->default('2023-01-01 00:00:00')->nullable(false);
            $table->text('upload')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};
