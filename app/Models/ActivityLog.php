<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';
    protected $primaryKey = 'id_log';
    protected $fillable = ['id_pengguna', 'aktivitas', 'modul', 'data_diproses', 'waktu'];
    protected $casts = [
        'data_diproses' => 'array',
        'waktu' => 'datetime',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function getAktivitasLabelAttribute(): string
    {
        return match ($this->aktivitas) {
            'create' => 'Tambah',
            'update' => 'Edit',
            'delete' => 'Hapus',
            default => ucfirst($this->aktivitas),
        };
    }

    public function getModulLabelAttribute(): string
    {
        return match ($this->modul) {
            'barang' => 'Barang',
            'pemasok' => 'Pemasok',
            'penerimaan_barang' => 'Penerimaan Barang',
            'transaksi_penjualan' => 'Transaksi Penjualan',
            default => ucfirst(str_replace('_', ' ', $this->modul)),
        };
    }

    public function getDataDiffAttribute(): ?array
    {
        if (!$this->data_diproses) return null;

        return [
            'old' => $this->data_diproses['old'] ?? null,
            'new' => $this->data_diproses['new'] ?? null,
        ];
    }

    public static function getModulLabelStatic(string $modul): string
    {
        return match ($modul) {
            'barang' => 'Barang',
            'pemasok' => 'Pemasok',
            'penerimaan_barang' => 'Penerimaan Barang',
            'transaksi_penjualan' => 'Transaksi Penjualan',
            default => ucfirst(str_replace('_', ' ', $modul)),
        };
    }
}