<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number', 'customer_id', 'branch_id', 'user_id',
        'subtotal', 'discount_total', 'tax_total', 'grand_total', 'currency',
        'status', 'payment_status', 'delivery_status',
        'payment_method', 'delivery_method',
        'customer_note', 'admin_note',
        'placed_at', 'paid_at', 'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'placed_at' => 'datetime',
        'paid_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Recalculate subtotal / discount / grand total from the order's line items
     * and synchronise the customer's spend totals.
     */
    public function recalculateTotals(bool $persist = true): void
    {
        $this->loadMissing('items');

        $subtotal = (float) $this->items->sum(fn ($i) => (float) $i->unit_price * (int) $i->quantity);
        $discount = (float) $this->items->sum('discount');
        $tax = (float) $this->tax_total;
        $grand = max(0, $subtotal - $discount + $tax);

        $this->subtotal = $subtotal;
        $this->discount_total = $discount;
        $this->grand_total = $grand;

        if ($persist) {
            $this->save();
        }
    }

    /**
     * Generate a unique sequential order number for new orders.
     */
    public static function generateOrderNumber(): string
    {
        do {
            $candidate = 'ORD-'.now()->format('ymd').'-'.strtoupper(Str::random(5));
        } while (static::query()->where('order_number', $candidate)->exists());

        return $candidate;
    }
}
