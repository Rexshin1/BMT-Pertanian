<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'pemilik_id',
        'satuan',
        'harga_jual',
    ];

    protected $casts = [
        'stok' => 'decimal:3',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
    ];

    public function pembelians(): HasMany
    {
        return $this->hasMany(Pembelian::class);
    }

    public function penjuwalans(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }

    public function pemilik(): BelongsTo
    {
        return $this->belongsTo(Pemilik::class);
    }
}
