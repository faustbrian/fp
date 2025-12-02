<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_slice;
use function array_values;
use function is_array;

/**
 * Returns the last element from an array or iterable.
 *
 * Retrieves the final element from an array or iterable sequence. The implementation
 * optimizes for arrays by using array_slice() to extract the last element directly,
 * while other iterables are fully traversed to reach the final value.
 *
 * Returns null for empty collections. Note that this means null cannot be distinguished
 * from an empty collection versus a collection whose last element is null.
 *
 * ```php
 * // Get last element from arrays
 * last([1, 2, 3, 4]); // 4
 * last(['a']); // 'a'
 * last([]); // null
 *
 * // Works with associative arrays (value only)
 * last(['a' => 1, 'b' => 2, 'c' => 3]); // 3
 *
 * // Use with generators
 * $gen = (function() {
 *     yield 1;
 *     yield 2;
 *     yield 3;
 * })();
 * last($gen); // 3
 * ```
 *
 * @param  iterable<mixed> $it The iterable to extract the last element from. Can be
 *                             an array, generator, or any object implementing Traversable.
 * @return mixed           the last element in the iterable, or null if the iterable
 *                         is empty or if the last element itself is null
 */
function last(iterable $it): mixed
{
    if (is_array($it)) {
        if ([] === $it) {
            return null;
        }

        return array_values(array_slice($it, -1))[0] ?? null;
    }

    $last = null;

    foreach ($it as $v) {
        $last = $v;
    }

    return $last;
}
