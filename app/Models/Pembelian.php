<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        'produk_id',
        'tanggal',
        'satuan',
        'jumlah',
        'harga_beli',
        'total_harga',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:3',
        'harga_beli' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }
}
