<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    protected $fillable = ['id_transaksi', 'total_bayar', 'metode_pembayaran', 'status_pembayaran', 'tanggal_bayar'];
    protected $casts = [
        'total_bayar' => 'decimal:2',
        'metode_pembayaran' => 'string',
        'status_pembayaran' => 'string',
        'tanggal_bayar' => 'date',
    ];

    public function transaksiPenjualan(): BelongsTo
    {
        return $this->belongsTo(TransaksiPenjualan::class, 'id_transaksi', 'id_transaksi');
    }

    public function isTunai(): bool
    {
        return $this->metode_pembayaran === 'tunai';
    }

    public function isLunas(): bool
    {
        return $this->status_pembayaran === 'lunas';
    }
}