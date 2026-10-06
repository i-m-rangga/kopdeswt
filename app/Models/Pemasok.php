<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemasok extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'pemasok';
    protected $primaryKey = 'id_pemasok';
    protected $fillable = ['nama_pemasok', 'kontak', 'alamat'];

    public function penerimaanBarang(): HasMany
    {
        return $this->hasMany(PenerimaanBarang::class, 'id_pemasok', 'id_pemasok');
    }

    protected function getLogModul(): string
    {
        return 'pemasok';
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

        return [
            'new' => $attributes,
            'old' => $oldData ?: null,
        ];
    }
}