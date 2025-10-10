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
 * Creates a closure that reduces an iterable with early termination.
 *
 * Performs a standard reduction operation but stops iteration early when the
 * stop condition returns true. The stop predicate is evaluated after each
 * accumulation step, checking the running accumulated value. This enables
 * short-circuit reductions for performance optimization or conditional
 * aggregation logic.
 *
 * Useful for finding first occurrences, accumulating until a threshold,
 * or implementing early-exit conditions in reduction operations without
 * processing the entire collection.
 *
 * ```php
 * // Sum until reaching 10
 * $sumUntil10 = reduceUntil(
 *     0,
 *     fn($acc, $n) => $acc + $n,
 *     fn($acc) => $acc >= 10
 * );
 * $result = $sumUntil10([3, 4, 5, 6]); // 12 (stops after 3+4+5)
 *
 * // Concatenate strings until length exceeds limit
 * $concatUntilLength = reduceUntil(
 *     '',
 *     fn($acc, $s) => $acc . $s,
 *     fn($acc) => strlen($acc) > 20
 * );
 * $text = $concatUntilLength(['Hello', ' ', 'World', ' ', 'foo', 'bar']);
 * ```
 *
 * @param  mixed    $init initial value for the accumulator, serving as the starting point
 *                        for the reduction operation and returned if the iterable is empty
 * @param  callable $c    Reducer function with signature (mixed $accumulator, mixed $value): mixed.
 *                        Called for each element to update the accumulated result.
 * @param  callable $stop Predicate function with signature (mixed $accumulator): bool.
 *                        When it returns true, iteration stops and the current accumulator
 *                        value is returned immediately without processing remaining elements.
 * @return Closure  a function accepting an iterable that returns the final accumulated value,
 *                  either after processing all elements or when the stop condition triggers
 */
function reduceUntil(mixed $init, callable $c, callable $stop): Closure
{
    return static function (iterable $it) use ($init, $c, $stop): mixed {
        foreach ($it as $v) {
            $init = $c($init, $v);

            if ($stop($init)) {
                return $init;
            }
        }

        return $init;
    };
}
