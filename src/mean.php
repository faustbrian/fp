<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\average;

/**
 * Calculates the arithmetic mean of a collection of numeric values.
 *
 * Computes the average by summing all values and dividing by the count.
 * This function is an alias for average() provided for statistical terminology
 * preference. Returns null for empty collections to avoid division by zero errors.
 *
 * ```php
 * mean([1, 2, 3, 4, 5]); // 3
 * mean([10, 20, 30]); // 20
 * mean([2.5, 3.5, 4.0]); // 3.333...
 * mean([]); // null
 *
 * // Statistical analysis
 * $testScores = [85, 92, 78, 90, 88];
 * $averageScore = mean($testScores); // 86.6
 * ```
 *
 * @param  iterable<float|int> $values Collection of numeric values to average.
 *                                     Accepts arrays, iterators, and generators
 *                                     containing integers and/or floats.
 * @return null|float|int      The arithmetic mean of all values, or null if
 *                             the collection is empty
 *
 * @see average() The underlying implementation function
 */
function mean(iterable $values): int|float|null
{
    return average($values);
}
