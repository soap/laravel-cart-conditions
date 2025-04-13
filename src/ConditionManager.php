<?php

namespace Soap\LaravelCartConditions;

class ConditionManager
{
    protected array $conditions = [];

    /**
     * Add a condition.
     */
    public function add(CartCondition $condition): void
    {
        $this->conditions[] = $condition;
    }

    /**
     * Applies matching conditions to a target's property.
     *
     * @param  object  $target  A cart or cart item having a property (e.g., subtotal, total).
     * @param  string  $baseKey  The property of the target to adjust.
     * @return float The final amount after applying all conditions.
     */
    public function applyTo(object $target, string $baseKey): float
    {
        // Assume the target has the property (for example, $target->subtotal)
        $base = $target->{$baseKey};

        foreach ($this->conditions as $condition) {
            if ($condition->target === $baseKey && $condition->appliesTo($target)) {
                $base = $condition->apply($base);
            }
        }

        return $base;
    }

    /**
     * Returns all added conditions.
     */
    public function all(): array
    {
        return $this->conditions;
    }
}
