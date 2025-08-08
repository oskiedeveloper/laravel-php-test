<?php

// Nested Eloquent Search Filter
// Build a flexible filter class that accepts a JSON filter and applies it across multiple relationships.
// using PHP Laravel, something like Nested Eloquent Search Filter.

// Notes: 
// 1. Works on Appointment::query()
// 2. Uses dot notation to apply filters across relations.
// 3. Must handle where, whereHas, and orWhereHas with fallback to where on the current model.
// 4. Include a test that asserts correct SQL is generated.


namespace App\Utils\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class NestedFilter
{
    protected Builder $query;

    /**
     * Apply the nested filter to the query.
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    public function apply(Builder $query, array $filters): Builder
    {
        $this->query = $query;

        foreach ($filters as $field => $value) {
            $this->applyFilter($field, $value);
        }

        return $this->query;
    }

    /**
     * Apply a single filter based on the field and value.
     * @param string $field
     * @param mixed $value
     * @return void
     */
    protected function applyFilter(string $field, $value): void
    {
        $segments = explode('.', $field);

        if (count($segments) === 1) {
            $this->query->where($field, $value);
            return;
        }

        $relation = array_shift($segments);
        $remainingField = implode('.', $segments);

        $this->query->whereHas($relation, function ($query) use ($remainingField, $value) {
            (new self)->apply($query, [$remainingField => $value]);
        });
    }
}