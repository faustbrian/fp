<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function hrtime;

/**
 * Creates a throttle wrapper that limits function execution frequency.
 *
 * Returns a curried function that wraps any callable to execute at most once
 * per specified time period. Uses high-resolution timing (hrtime) for precise
 * throttling. Subsequent calls within the throttle window return the cached
 * result from the last execution without re-invoking the function.
 *
 * Ideal for rate-limiting expensive operations like API calls, database queries,
 * file I/O, or event handlers that fire frequently but don't need to execute
 * every time.
 *
 * ```php
 * // Throttle to once per second (1,000,000 microseconds)
 * $throttled = throttle(1_000_000)(fn() => expensive_api_call());
 * $throttled(); // Executes and caches result
 * $throttled(); // Returns cached result (within 1 second)
 * sleep(2);
 * $throttled(); // Executes again (throttle window expired)
 *
 * // Throttle event handler
 * $handleClick = throttle(500_000)(function($event) {
 *     processEvent($event);
 * });
 * ```
 *
 * @param  int     $microseconds The minimum time between executions in microseconds.
 *                               Must be positive. Use 1_000_000 for 1 second throttling.
 *                               Higher values mean less frequent execution.
 * @return Closure A function that accepts a callable and returns a throttled version.
 *                 The throttled function maintains state across calls to track timing
 *                 and cache results, executing only when the time window has elapsed.
 */
function throttle(int $microseconds): Closure
{
    return static function (callable $fn) use ($microseconds): Closure {
        $lastExecutionTime = null;
        $lastResult = null;

        return static function (...$args) use ($fn, $microseconds, &$lastExecutionTime, &$lastResult): mixed {
            $currentTime = hrtime(true);

            // If this is the first call or enough time has passed, execute
            if ($lastExecutionTime === null || (($currentTime - $lastExecutionTime) / 1_000) >= $microseconds) {
                $lastExecutionTime = $currentTime;
                $lastResult = $fn(...$args);
            }

            return $lastResult;
        };
    };
}
