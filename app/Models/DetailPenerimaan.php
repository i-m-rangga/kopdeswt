<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPenerimaan extends Model
{
    use HasFactory;

    protected $table = 'detail_penerimaan';
    protected $primaryKey = 'id_detail';
    protected $fillable = ['id_penerimaan', 'id_barang', 'jumlah', 'harga_beli'];
    protected $casts = [
        'jumlah' => 'integer',
        'harga_beli' => 'decimal:2',
    ];

    public function penerimaanBarang(): BelongsTo
    {
        return $this->belongsTo(PenerimaanBarang::class, 'id_penerimaan', 'id_penerimaan');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function getSubtotalAttribute(): float
    {
        return $this->jumlah * $this->harga_beli;
    }
}