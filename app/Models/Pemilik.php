<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemilik extends Model
{
    protected $fillable = [
        'kode',
        'nama',
    ];

    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class);
    }

    public function getIdTampilanAttribute(): string
    {
        return str_pad($this->kode, 3, '0', STR_PAD_LEFT);
    }
}
