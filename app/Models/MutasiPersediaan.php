<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiPersediaan extends Model
{
    use HasFactory;

    protected $table = 'mutasi_persediaan';
    protected $primaryKey = 'id_mutasi';
    protected $fillable = ['id_barang', 'tanggal', 'jenis_mutasi', 'jumlah', 'stok_akhir', 'referensi'];
    protected $casts = [
        'tanggal' => 'date',
        'jenis_mutasi' => 'string',
        'jumlah' => 'integer',
        'stok_akhir' => 'integer',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function isMasuk(): bool
    {
        return $this->jenis_mutasi === 'masuk';
    }

    public function isKeluar(): bool
    {
        return $this->jenis_mutasi === 'keluar';
    }

    /**
     * Buat catatan mutasi barang masuk (dari penerimaan)
     */
    public static function catatBarangMasuk(int $idBarang, int $jumlah, int $stokAkhir, string $referensi, string $tanggal = null): self
    {
        return self::create([
            'id_barang' => $idBarang,
            'tanggal' => $tanggal ?? now()->toDateString(),
            'jenis_mutasi' => 'masuk',
            'jumlah' => $jumlah,
            'stok_akhir' => $stokAkhir,
            'referensi' => $referensi,
        ]);
    }

    /**
     * Buat catatan mutasi barang keluar (dari penjualan)
     */
    public static function catatBarangKeluar(int $idBarang, int $jumlah, int $stokAkhir, string $referensi, string $tanggal = null): self
    {
        return self::create([
            'id_barang' => $idBarang,
            'tanggal' => $tanggal ?? now()->toDateString(),
            'jenis_mutasi' => 'keluar',
            'jumlah' => $jumlah,
            'stok_akhir' => $stokAkhir,
            'referensi' => $referensi,
        ]);
    }
}