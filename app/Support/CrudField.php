<?php

namespace App\Support;

/**
 * Lightweight value object describing one field on a CRUD form.
 * Used by the generic admin form view to render a consistent UI across modules.
 */
final class CrudField
{
    public function __construct(
        public string $name,
        public string $label,
        public string $type = 'text',
        public bool $required = false,
        public mixed $default = null,
        /** @var array<int|string, string> */
        public array $options = [],
        public ?string $relation = null,
        public ?string $relationLabel = null,
        public ?string $help = null,
        public string $colClass = 'col-md-6',
        public ?string $placeholder = null,
        public bool $tomSelect = true,
        public ?string $rows = null,
        public ?string $accept = null,
    ) {}

    public static function text(string $name, string $label, bool $required = false, string $colClass = 'col-md-6'): self
    {
        return new self($name, $label, 'text', $required, colClass: $colClass);
    }

    public static function textarea(string $name, string $label, bool $required = false, string $colClass = 'col-md-12', string $rows = '3'): self
    {
        return new self($name, $label, 'textarea', $required, colClass: $colClass, rows: $rows);
    }

    public static function richtext(string $name, string $label, bool $required = false, string $colClass = 'col-md-12'): self
    {
        return new self($name, $label, 'richtext', $required, colClass: $colClass);
    }

    public static function number(string $name, string $label, bool $required = false, string $colClass = 'col-md-3'): self
    {
        return new self($name, $label, 'number', $required, colClass: $colClass);
    }

    public static function decimal(string $name, string $label, bool $required = false, string $colClass = 'col-md-3'): self
    {
        return new self($name, $label, 'decimal', $required, colClass: $colClass);
    }

    public static function date(string $name, string $label, bool $required = false, string $colClass = 'col-md-3'): self
    {
        return new self($name, $label, 'date', $required, colClass: $colClass);
    }

    public static function time(string $name, string $label, bool $required = false, string $colClass = 'col-md-3'): self
    {
        return new self($name, $label, 'time', $required, colClass: $colClass);
    }

    public static function datetime(string $name, string $label, bool $required = false, string $colClass = 'col-md-3'): self
    {
        return new self($name, $label, 'datetime', $required, colClass: $colClass);
    }

    public static function select(string $name, string $label, array $options, bool $required = false, string $colClass = 'col-md-4'): self
    {
        return new self($name, $label, 'select', $required, options: $options, colClass: $colClass);
    }

    public static function multiselect(string $name, string $label, array $options, string $colClass = 'col-md-6'): self
    {
        return new self($name, $label, 'multiselect', false, options: $options, colClass: $colClass);
    }

    public static function relation(string $name, string $label, string $relation, string $relationLabel = 'name', bool $required = false, string $colClass = 'col-md-4'): self
    {
        return new self($name, $label, 'relation', $required, relation: $relation, relationLabel: $relationLabel, colClass: $colClass);
    }

    public static function checkbox(string $name, string $label, string $colClass = 'col-md-3'): self
    {
        return new self($name, $label, 'checkbox', colClass: $colClass);
    }

    public static function password(string $name, string $label, bool $required = false, string $colClass = 'col-md-6'): self
    {
        return new self($name, $label, 'password', $required, colClass: $colClass);
    }

    public static function email(string $name, string $label, bool $required = false, string $colClass = 'col-md-6'): self
    {
        return new self($name, $label, 'email', $required, colClass: $colClass);
    }

    public static function image(string $name, string $label, bool $required = false, string $colClass = 'col-md-6'): self
    {
        return new self($name, $label, 'image', $required, colClass: $colClass, accept: 'image/*');
    }

    public static function file(string $name, string $label, bool $required = false, string $colClass = 'col-md-6', ?string $accept = null): self
    {
        return new self($name, $label, 'file', $required, colClass: $colClass, accept: $accept);
    }

    public static function color(string $name, string $label, bool $required = false, string $colClass = 'col-md-3'): self
    {
        return new self($name, $label, 'color', $required, colClass: $colClass);
    }
}
