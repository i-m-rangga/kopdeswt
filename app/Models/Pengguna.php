<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengguna extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    protected $fillable = ['nama', 'username', 'password', 'role', 'status'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'password' => 'hashed',
        'role' => 'string',
        'status' => 'string',
    ];

    /**
     * Get the username/login identifier.
     */
    public function getAuthIdentifierName(): string
    {
        return 'username';
    }

    public function penerimaanSebagaiPetugas(): HasMany
    {
        return $this->hasMany(PenerimaanBarang::class, 'id_petugas', 'id_pengguna');
    }

    public function transaksiSebagaiKasir(): HasMany
    {
        return $this->hasMany(TransaksiPenjualan::class, 'id_kasir', 'id_pengguna');
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    public function isPetugasPersediaan(): bool
    {
        return $this->role === 'petugas_persediaan';
    }

    public function isPengurus(): bool
    {
        return $this->role === 'pengurus';
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}