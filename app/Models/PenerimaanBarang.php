<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenerimaanBarang extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'penerimaan_barang';
    protected $primaryKey = 'id_penerimaan';
    protected $fillable = ['tanggal', 'id_pemasok', 'id_petugas'];
    protected $casts = ['tanggal' => 'date'];

    public function pemasok(): BelongsTo
    {
        return $this->belongsTo(Pemasok::class, 'id_pemasok', 'id_pemasok');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_petugas', 'id_pengguna');
    }

    public function detailPenerimaan(): HasMany
    {
        return $this->hasMany(DetailPenerimaan::class, 'id_penerimaan', 'id_penerimaan');
    }

    protected function getLogModul(): string
    {
        return 'penerimaan_barang';
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
        $attributes['pemasok_nama'] = $this->pemasok?->nama_pemasok;
        $attributes['petugas_nama'] = $this->petugas?->nama;

        return [
            'new' => $attributes,
            'old' => $oldData ?: null,
        ];
    }
}