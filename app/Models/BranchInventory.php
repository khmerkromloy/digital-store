<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Per-branch inventory record. Maps onto the `branch_product` pivot table.
 */
class BranchInventory extends Model
{
    use HasFactory;

    protected $table = 'branch_product';

    protected $fillable = [
        'branch_id', 'product_id', 'stock', 'price_override', 'is_active',
    ];

    protected $casts = [
        'stock' => 'integer',
        'price_override' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
