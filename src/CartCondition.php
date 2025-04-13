<?php

namespace Soap\LaravelCartConditions;

class CartCondition
{
    public function __construct(
        public string $name,
        public string $target,  // e.g., 'price', 'subtotal', 'total', 'tax' or 'shipping'
        public string $type,    // e.g., 'discount', 'surcharge', 'fee'
        public string $operator, // Mathematical operator: '+', '-', '*', '/', '%'
        public float $value,
        public ?\Closure $when = null // Optional: condition callback, returns true if condition applies
    ) {}

    /**
     * Checks if the condition applies to the given target (cart or cart item).
     */
    public function appliesTo($target): bool
    {
        return $this->when ? ($this->when)($target) : true;
    }

    /**
     * Applies the condition to the given base value.
     */
    public function apply(float $base): float
    {
        return match ($this->operator) {
            '+' => $base + $this->value,
            '-' => $base - $this->value,
            '*' => $base * $this->value,
            '/' => $base / $this->value,
            '%' => $base - ($base * $this->value / 100),
            default => $base,
        };
    }
}
