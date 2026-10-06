<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerimaan_barang', function (Blueprint $table) {
            $table->id('id_penerimaan');
            $table->date('tanggal');
            $table->foreignId('id_pemasok')->constrained('pemasok', 'id_pemasok')->nullOnDelete();
            $table->foreignId('id_petugas')->constrained('pengguna', 'id_pengguna')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerimaan_barang');
    }
};