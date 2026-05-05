<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

/**
 * Renders a column for a table row.
 * Supports `relation.field` dot notation, casted dates, booleans, status badges,
 * money decimals, and html-escapes everything else.
 */
final class ColumnRenderer
{
    private const STATUS_BADGES = [
        'active' => 'success',
        'inactive' => 'secondary',
        'blocked' => 'danger',
        'available' => 'success',
        'reserved' => 'warning',
        'sold' => 'info',
        'expired' => 'secondary',
        'invalid' => 'danger',
        'pending' => 'warning',
        'paid' => 'success',
        'partial' => 'info',
        'unpaid' => 'danger',
        'refunded' => 'secondary',
        'cancelled' => 'secondary',
        'delivered' => 'success',
        'failed' => 'danger',
        'new' => 'info',
        'read' => 'secondary',
        'replied' => 'success',
        'spam' => 'danger',
        'succeeded' => 'success',
    ];

    /**
     * Render a status badge for any value mapped in self::STATUS_BADGES.
     * Falls back to a neutral secondary badge so unknown statuses still display.
     */
    public static function badge(?string $value, ?string $i18nPrefix = null): string
    {
        if ($value === null || $value === '') {
            return '<span class="text-muted">—</span>';
        }
        $color = self::STATUS_BADGES[$value] ?? 'secondary';
        $label = e($value);
        $attr = $i18nPrefix
            ? ' data-i18n="'.e($i18nPrefix.'.'.$value).'"'
            : '';

        return '<span class="badge bg-'.$color.'"'.$attr.'>'.$label.'</span>';
    }

    /**
     * Render a money value with optional currency prefix and 2-decimal precision.
     */
    public static function money($value, ?string $currency = null): string
    {
        if ($value === null || $value === '') {
            return '<span class="text-muted">—</span>';
        }
        $formatted = number_format((float) $value, 2);
        $prefix = $currency ? '<span class="text-muted small me-1">'.e($currency).'</span>' : '';

        return '<span class="fw-medium">'.$prefix.e($formatted).'</span>';
    }

    public static function render(Model $row, string $key): string
    {
        $value = data_get($row, $key);

        if ($value === null || $value === '') {
            return '<span class="text-muted">—</span>';
        }

        if (is_bool($value)) {
            return $value
                ? '<span class="badge bg-success">Yes</span>'
                : '<span class="badge bg-secondary">No</span>';
        }

        if ($value instanceof \DateTimeInterface) {
            return e($value->format('Y-m-d H:i'));
        }

        if (is_array($value)) {
            return e(json_encode($value));
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return e((string) $value);
        }

        if (str_ends_with($key, 'price') || str_ends_with($key, 'amount') || str_ends_with($key, '_total') || $key === 'total') {
            return '<span class="fw-medium">'.e(number_format((float) $value, 2)).'</span>';
        }

        $escaped = e((string) $value);

        if ((str_ends_with($key, 'status') || $key === 'payment_status' || $key === 'delivery_status')
            && isset(self::STATUS_BADGES[$value])) {
            return '<span class="badge bg-'.self::STATUS_BADGES[$value].'">'.$escaped.'</span>';
        }

        return $escaped;
    }
}
