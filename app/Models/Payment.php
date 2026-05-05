<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id', 'payment_number', 'method', 'amount', 'currency',
        'status', 'reference_no', 'proof_image', 'note', 'paid_at', 'verified_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public static function generatePaymentNumber(): string
    {
        do {
            $candidate = 'PAY-'.now()->format('ymd').'-'.strtoupper(Str::random(5));
        } while (static::query()->where('payment_number', $candidate)->exists());

        return $candidate;
    }
}
