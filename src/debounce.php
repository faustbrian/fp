<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;
use Illuminate\Support\Sleep;

/**
 * Creates a debounced function wrapper that delays execution by a specified duration.
 *
 * Returns a curried function that accepts a callable and produces a debounced version.
 * The debounced function introduces a deliberate delay before execution, useful for
 * rate-limiting operations, preventing excessive API calls, or ensuring minimum intervals
 * between operations in CLI scripts.
 *
 * Note: PHP's synchronous execution model means this implementation blocks for the
 * entire debounce period. Unlike JavaScript debouncing, this doesn't cancel pending
 * executions—it simply enforces a delay. Best suited for CLI contexts where you need
 * guaranteed minimum intervals between operations.
 *
 * ```php
 * $debounced = debounce(500000)($apiCall); // 500ms delay
 * $debounced($params); // Waits 500ms, then executes
 * ```
 *
 * @param  int     $microseconds The delay duration in microseconds before the wrapped function
 *                               executes. For example, 1000000 microseconds equals 1 second.
 *                               Use 0 to disable the delay while maintaining the wrapper structure.
 * @return Closure A curried function that accepts a callable and returns a closure.
 *                 The returned closure accepts the same arguments as the original callable
 *                 but delays execution by the specified microseconds.
 */
function debounce(int $microseconds): Closure
{
    return static fn (callable $fn): Closure => static function (...$args) use ($fn, $microseconds): mixed {
        // Always wait for the debounce period before executing
        if ($microseconds > 0) {
            Sleep::usleep($microseconds);
        }

        // Execute with the provided arguments
        return $fn(...$args);
    };
}
