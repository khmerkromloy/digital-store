<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'key_value',
        'status',
        'sold_at',
        'sold_to_user_id',
    ];

    protected $casts = [
        'sold_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_to_user_id');
    }
}
