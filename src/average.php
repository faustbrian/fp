<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

/**
 * Calculates the arithmetic mean (average) of numeric values in an iterable.
 *
 * Computes the sum of all values divided by the count. Returns null for
 * empty iterables to distinguish between a zero average and no data. This
 * function accepts any iterable, not just arrays, making it suitable for use
 * with generators and other iterable types. The function performs a single
 * pass through the data for O(n) time complexity.
 *
 * ```php
 * average([1, 2, 3, 4, 5]); // 3
 * average([10, 20, 30]); // 20
 * average([]); // null
 *
 * // Works with generators for memory efficiency
 * $numbers = function() { foreach (range(1, 1000) as $n) yield $n; };
 * average($numbers()); // 500.5
 *
 * // Useful for statistics
 * $scores = [85, 90, 78, 92, 88];
 * average($scores); // 86.6
 * ```
 *
 * @param  iterable<float|int> $values Iterable of numeric values to average.
 *                                     Empty iterables return null. All values
 *                                     should be numeric for correct calculation.
 *                                     Non-numeric values may cause type errors.
 * @return null|float|int      The arithmetic mean of the values, or null if the
 *                             iterable is empty. Returns int if division is exact
 *                             (no remainder), float otherwise.
 */
function average(iterable $values): int|float|null
{
    $total = 0;
    $count = 0;

    foreach ($values as $value) {
        $total += $value;
        ++$count;
    }

    return $count > 0 ? $total / $count : null;
}
