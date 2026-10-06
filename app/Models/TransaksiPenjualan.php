<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransaksiPenjualan extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'transaksi_penjualan';
    protected $primaryKey = 'id_transaksi';
    protected $fillable = ['tanggal', 'id_kasir', 'total_transaksi'];
    protected $casts = [
        'tanggal' => 'date',
        'total_transaksi' => 'decimal:2',
    ];

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_kasir', 'id_pengguna');
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi');
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'id_transaksi', 'id_transaksi');
    }

    public function mutasiPersediaan(): HasMany
    {
        return $this->hasMany(MutasiPersediaan::class, 'referensi', 'id_transaksi')
            ->where('jenis_mutasi', 'keluar');
    }

    protected function getLogModul(): string
    {
        return 'transaksi_penjualan';
    }

    protected function getLogData(): array
    {
        $attributes = $this->getAttributes();
        $hidden = array_merge($this->getHidden(), ['password', 'remember_token']);
        foreach ($hidden as $field) {
            unset($attributes[$field]);
        }

        $oldData = [];
        if ($this->wasChanged()) {
            foreach ($this->getChanges() as $key => $value) {
                $oldData[$key] = $this->getOriginal($key);
            }
        }

        // Add related data for context
        $attributes['kasir_nama'] = $this->kasir?->nama;
        $attributes['items_count'] = $this->detailTransaksi()->count();
        if ($this->pembayaran) {
            $attributes['metode_pembayaran'] = $this->pembayaran->metode_pembayaran;
            $attributes['total_bayar'] = $this->pembayaran->total_bayar;
        }

        return [
            'new' => $attributes,
            'old' => $oldData ?: null,
        ];
    }
}