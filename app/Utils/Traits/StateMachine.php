<?php

namespace App\Utils\Traits;

use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use App\Events\ModelTransitioning;
use App\Events\ModelTransitioned;

trait StateMachine
{
    public function transitionTo(string $newState): void
    {
        $currentState = $this->state;

        if (!static::isTransitionAllowed($currentState, $newState)) {
            throw new \InvalidArgumentException("Invalid state transition from '$currentState' to '$newState'.");
        }

        // Dispatch before-transition event
        Event::dispatch(new ModelTransitioning($this, $currentState, $newState));

        // Perform the transition
        $this->state = $newState;
        $this->save();

        // Dispatch after-transition event
        Event::dispatch(new ModelTransitioned($this, $currentState, $newState));
    }

    public static function isTransitionAllowed(string $from, string $to): bool
    {
        $states = static::$states ?? [];

        return isset($states[$from]) && in_array($to, $states[$from]);
    }
}