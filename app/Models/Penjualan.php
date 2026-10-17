<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjuwalans';

    protected $fillable = [
        'produk_id',
        'tanggal',
        'satuan',
        'jumlah',
        'harga_beli_satuan',
        'harga_jual',
        'total_harga_beli',
        'total_harga_jual',
        'laba',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:3',
        'harga_beli_satuan' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'total_harga_beli' => 'decimal:2',
        'total_harga_jual' => 'decimal:2',
        'laba' => 'decimal:2',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }
}
