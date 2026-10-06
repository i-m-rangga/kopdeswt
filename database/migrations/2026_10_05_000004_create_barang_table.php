<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id('id_barang');
            $table->foreignId('id_kategori')->constrained('kategori_barang', 'id_kategori')->nullOnDelete();
            $table->string('nama_barang', 100);
            $table->string('satuan', 20);
            $table->decimal('harga', 12, 2);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};