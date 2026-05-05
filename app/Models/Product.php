<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'name_kh',
        'slug',
        'sku',
        'short_description',
        'short_description_kh',
        'description',
        'description_kh',
        'price',
        'original_price',
        'currency',
        'product_type',
        'cover_image',
        'images',
        'stock',
        'view_count',
        'sales_count',
        'is_active',
        'is_featured',
        'auto_deliver',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'stock' => 'integer',
        'view_count' => 'integer',
        'sales_count' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'auto_deliver' => 'boolean',
        'images' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (empty($product->slug) && ! empty($product->name)) {
                $product->slug = Str::slug($product->name).'-'.Str::lower(Str::random(4));
            }
            if (empty($product->sku) && ! empty($product->slug)) {
                $product->sku = Str::upper(Str::random(8));
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function keys(): HasMany
    {
        return $this->hasMany(ProductKey::class);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_product')
            ->withPivot(['stock', 'price_override', 'is_active'])
            ->withTimestamps();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return (int) round((($this->original_price - $this->price) / $this->original_price) * 100);
        }

        return null;
    }

    public function getLocalisedNameAttribute(): string
    {
        if (app()->getLocale() === 'km' && filled($this->name_kh)) {
            return $this->name_kh;
        }

        return $this->name;
    }
}
