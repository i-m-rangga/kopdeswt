<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penerimaan', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_penerimaan')->constrained('penerimaan_barang', 'id_penerimaan')->cascadeOnDelete();
            $table->foreignId('id_barang')->constrained('barang', 'id_barang')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->decimal('harga_beli', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penerimaan');
    }
};