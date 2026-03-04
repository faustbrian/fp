<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

/**
 * Creates a function that executes a side effect, then returns the original value unchanged.
 *
 * Returns a curried function that accepts a value, passes it to the provided
 * callable for side effects (logging, debugging, mutation, etc.), then returns
 * the original value. This allows inserting operations into a pipeline without
 * breaking the data flow, making it ideal for debugging or triggering actions
 * based on intermediate values.
 *
 * ```php
 * $logger = tap(fn($x) => error_log("Value: $x"));
 * $logger(42); // Logs "Value: 42", returns 42
 *
 * // In a pipeline
 * $result = pipe(
 *     [1, 2, 3],
 *     map(fn($x) => $x * 2),
 *     tap(fn($arr) => var_dump($arr)), // Debug without breaking flow
 *     sum()
 * ); // Returns: 12
 * ```
 *
 * @param  callable(mixed): void $sideEffect A function that receives the value for side effects.
 *                                           The return value of this function is ignored.
 *                                           Common uses include logging, debugging, or mutations.
 * @return Closure               a function that accepts any value, executes the side effect with it,
 *                               and returns the original value unchanged regardless of what the
 *                               side effect function does or returns
 */
function tap(callable $sideEffect): Closure
{
    return static function (mixed $value) use ($sideEffect): mixed {
        $sideEffect($value);

        return $value;
    };
}
