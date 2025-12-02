<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_map;
use function array_values;
use function min;

/**
 * Combine multiple arrays element-wise into tuples.
 *
 * Merges multiple arrays by grouping elements at corresponding positions into
 * tuples (sub-arrays). The first element of each input array forms the first
 * tuple, the second elements form the second tuple, and so on. The operation
 * stops at the length of the shortest input array to ensure all tuples are
 * complete. This is commonly used for parallel iteration or creating coordinate
 * pairs from separate dimension arrays.
 *
 * ```php
 * $numbers = [1, 2, 3];
 * $letters = ['a', 'b', 'c'];
 * $paired = zip($numbers, $letters);
 * // Result: [[1, 'a'], [2, 'b'], [3, 'c']]
 *
 * $ids = [101, 102, 103, 104];
 * $names = ['Alice', 'Bob'];
 * $statuses = ['active', 'inactive', 'pending'];
 * $combined = zip($ids, $names, $statuses);
 * // Result: [[101, 'Alice', 'active'], [102, 'Bob', 'inactive']]
 * // Stops at 2 elements (shortest array)
 * ```
 *
 * @param  array<mixed>             ...$arrays Variable number of arrays to combine. Each array's
 *                                             elements at position N will be grouped together in
 *                                             the Nth tuple of the result.
 * @return array<int, array<mixed>> Array of tuples where each tuple contains one element
 *                                  from each input array at the corresponding position.
 *                                  Returns empty array if no arrays are provided.
 *
 * @since 1.0.0
 */
function zip(array ...$arrays): array
{
    if ($arrays === []) {
        return [];
    }

    $result = [];
    $minLength = min(array_map(count(...), $arrays));

    for ($i = 0; $i < $minLength; ++$i) {
        $tuple = [];

        foreach ($arrays as $array) {
            $tuple[] = array_values($array)[$i];
        }

        $result[] = $tuple;
    }

    return $result;
}
