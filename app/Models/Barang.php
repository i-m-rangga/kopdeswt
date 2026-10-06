<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    protected $fillable = ['id_kategori', 'nama_barang', 'satuan', 'harga', 'stok', 'stok_minimum'];
    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
        'stok_minimum' => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriBarang::class, 'id_kategori', 'id_kategori');
    }

    public function detailPenerimaan(): HasMany
    {
        return $this->hasMany(DetailPenerimaan::class, 'id_barang', 'id_barang');
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_barang', 'id_barang');
    }

    public function mutasiPersediaan(): HasMany
    {
        return $this->hasMany(MutasiPersediaan::class, 'id_barang', 'id_barang');
    }

    public function isStokRendah(): bool
    {
        return $this->stok <= $this->stok_minimum;
    }

    public function kurangiStok(int $jumlah): void
    {
        $this->decrement('stok', $jumlah);
    }

    public function tambahStok(int $jumlah): void
    {
        $this->increment('stok', $jumlah);
    }

    protected function getLogModul(): string
    {
        return 'barang';
    }

    protected function getLogData(): array
    {
        $attributes = $this->getAttributes();
        $hidden = array_merge($this->getHidden(), ['password', 'remember_token']);
        foreach ($hidden as $field) {
            unset($attributes[$field]);
        }

        // For update, include old values
        $oldData = [];
        if ($this->wasChanged()) {
            foreach ($this->getChanges() as $key => $value) {
                $oldData[$key] = $this->getOriginal($key);
            }
        }

        return [
            'new' => $attributes,
            'old' => $oldData ?: null,
        ];
    }
}