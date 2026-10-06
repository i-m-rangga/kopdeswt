<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id('id_log');
            $table->foreignId('id_pengguna')->constrained('pengguna', 'id_pengguna')->cascadeOnDelete();
            $table->string('aktivitas', 100); // create, update, delete
            $table->string('modul', 50); // barang, pemasok, penerimaan_barang, transaksi_penjualan
            $table->json('data_diproses')->nullable(); // old/new data snapshot
            $table->timestamp('waktu')->useCurrent();
        });

        // Index untuk performa filter
        $table->index(['id_pengguna', 'waktu']);
        $table->index(['modul', 'waktu']);
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};