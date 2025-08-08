<?php

namespace App\Services;

use App\Models\User;

class RuleEvaluator
{
    protected array $operators;

    public function __construct()
    {
        $this->operators = [
            '==' => fn($a, $b) => $a == $b,
            '!=' => fn($a, $b) => $a != $b,
            '>' => fn($a, $b) => $a > $b,
            '<' => fn($a, $b) => $a < $b,
            'in' => fn($a, $b) => in_array($a, (array)$b),
            'not_in' => fn($a, $b) => !in_array($a, (array)$b),
            'contains' => fn($a, $b) => Str::contains((string) $a, (string) $b)
        ];
    }

    /**
     *  Evaluate a set of rules against a model instance.
     *  
     *  @param User $user
     *  @param array $rules
     *  @return bool
     */ 
    public function evaluate(User $user, array $rules): bool
    {
        foreach ($rules as $rule) {
            if (!$this->evaluateRule($user, $rule)) {
                return false;
            }
        }
        return true;
    }

    /**
     *  Evaluate a single rule against the user or data.
     *  
     *  @param User $user
     *  @param array $rule
     *  @return bool
     */
    public function evaluateRule(User $user, array $rule): bool
    {
        $field = $rule['field'] ?? null;
        $operator = $rule['operator'] ?? null;
        $value = $rule['value'] ?? null;

        // Retrieve actual value from the user model
        $userValue = data_get($user, $field);

        // Normalize null-like strings to null
        if (is_string($userValue) && strtolower($userValue) === 'null') {
            $userValue = null;
        }

        // Ensure the operators are supported
        if (! isset($this->operators[$operator])) {
            throw new \InvalidArgumentException("Unsupported operator: {$operator}");
        }

        // Perform the comparison using the operator
        return call_user_func($this->operators[$operator], $userValue, $value);
    }
}
