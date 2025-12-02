<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

/**
 * Finds the maximum numeric value from an iterable collection.
 *
 * Iterates through all values to determine the largest numeric value present.
 * Works with arrays, iterators, and generators. Returns null when the input
 * is empty, allowing for safe handling of edge cases without throwing exceptions.
 * Uses strict comparison to ensure accurate max detection across integers and floats.
 *
 * ```php
 * maxValue([3, 7, 2, 9, 5]); // 9
 * maxValue([1.5, 2.3, 1.8]); // 2.3
 * maxValue([]); // null
 *
 * // Works with iterators
 * $generator = (function() {
 *     yield 10;
 *     yield 25;
 *     yield 15;
 * })();
 * maxValue($generator); // 25
 * ```
 *
 * @param  iterable<float|int> $values Collection of numeric values to search.
 *                                     Accepts arrays, iterators, and generators
 *                                     containing integers and/or floats.
 * @return null|float|int      The maximum value found, or null if the collection
 *                             is empty or contains no comparable values
 */
function maxValue(iterable $values): int|float|null
{
    $max = null;

    foreach ($values as $value) {
        if ($max === null || $value > $max) {
            $max = $value;
        }
    }

    return $max;
}
