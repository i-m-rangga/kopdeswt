<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->foreignId('id_transaksi')->unique()->constrained('transaksi_penjualan', 'id_transaksi')->cascadeOnDelete();
            $table->decimal('total_bayar', 12, 2);
            $table->enum('metode_pembayaran', ['tunai', 'non-tunai']);
            $table->enum('status_pembayaran', ['lunas', 'belum'])->default('lunas');
            $table->date('tanggal_bayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};