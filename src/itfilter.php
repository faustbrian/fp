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
 * Returns a higher-order function that filters an iterable based on a predicate function.
 *
 * This function creates a curried filter that applies a predicate to each value in an
 * iterable, yielding only the values for which the predicate returns true. The returned
 * closure accepts an iterable and produces a lazy filtered result. Keys are preserved
 * in the output.
 *
 * This function only passes values to the predicate, making it compatible with built-in
 * PHP functions like is_numeric(), is_string(), etc. For key-aware filtering, use itfilterWithKeys().
 *
 * ```php
 * // Filter even numbers
 * $evens = itfilter(fn($n) => $n % 2 === 0);
 * $result = $evens([1, 2, 3, 4, 5]); // [1 => 2, 3 => 4]
 *
 * // Filter with built-in function
 * $numbers = itfilter('is_numeric');
 * $result = $numbers(['a', '1', 'b', '2']); // [1 => '1', 3 => '2']
 *
 * // Default truthiness filter
 * $truthy = itfilter();
 * $result = $truthy([0, 1, false, true, '', 'hello']); // [1 => 1, 3 => true, 5 => 'hello']
 * ```
 *
 * @param  null|callable(mixed): bool  $c Optional predicate function that receives each value and returns
 *                                        true to include it or false to exclude it. Defaults to a truthiness
 *                                        check (casts value to bool) if not provided. Compatible with built-in
 *                                        PHP functions like is_numeric(), is_string(), etc.
 * @return Closure(iterable): iterable A curried function that accepts an iterable and returns a filtered
 *                                     iterable yielding only items where the predicate returned true.
 *                                     Preserves keys from the original iterable.
 *
 * @see itfilterWithKeys() For filtering with access to both keys and values in the predicate.
 */
function itfilter(?callable $c = null): Closure
{
    $c ??= static fn (mixed $v): bool => (bool) $v;

    return static function (iterable $it) use ($c): iterable {
        foreach ($it as $k => $v) {
            if ($c($v)) {
                yield $k => $v;
            }
        }
    };
}
