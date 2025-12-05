<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_values;
use function count;
use function floor;
use function is_array;
use function iterator_to_array;
use function sort;

/**
 * Calculates the median value from a collection of numeric values.
 *
 * Determines the middle value in a sorted dataset. For odd-length collections,
 * returns the middle element. For even-length collections, returns the arithmetic
 * mean of the two middle elements. Converts iterators to arrays and reindexes
 * to ensure proper sorting and median calculation. Returns null for empty
 * collections to maintain type safety.
 *
 * ```php
 * median([1, 3, 5, 7, 9]); // 5 (middle value)
 * median([2, 4, 6, 8]); // 5 (average of 4 and 6)
 * median([100]); // 100
 * median([]); // null
 *
 * // Unsorted input is handled automatically
 * median([9, 1, 5, 3, 7]); // 5
 *
 * // Statistical analysis
 * $salaries = [45000, 52000, 48000, 150000, 51000];
 * median($salaries); // 51000 (less affected by outliers than mean)
 * ```
 *
 * @param  iterable<float|int> $values Collection of numeric values to analyze.
 *                                     Accepts arrays, iterators, and generators.
 *                                     Values will be sorted internally without
 *                                     modifying the original collection.
 * @return null|float|int      The median value: middle element for odd-length
 *                             collections, average of two middle elements for
 *                             even-length, or null if empty
 */
function median(iterable $values): int|float|null
{
    $arr = is_array($values) ? $values : iterator_to_array($values);
    $arr = array_values($arr);

    if ($arr === []) {
        return null;
    }

    sort($arr);
    $count = count($arr);
    $middle = (int) floor($count / 2);

    if ($count % 2 === 0) {
        return ($arr[$middle - 1] + $arr[$middle]) / 2;
    }

    return $arr[$middle];
}
