<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_intersect;
use function array_values;
use function count;

/**
 * Returns the intersection of arrays, containing only values present in all provided arrays.
 *
 * This function computes the set intersection of multiple arrays, returning a new array
 * containing only the values that appear in every input array. Keys are not preserved;
 * the result is re-indexed with sequential numeric keys starting from zero.
 *
 * ```php
 * intersection([1, 2, 3], [2, 3, 4], [3, 4, 5]); // [3]
 * intersection([1, 2], [2, 3]); // [2]
 * intersection([1, 2]); // [1, 2]
 * intersection(); // []
 * ```
 *
 * @param  array<int|string, mixed> ...$arrays Variable number of arrays to intersect. If no arrays
 *                                             are provided, returns an empty array. If a single array
 *                                             is provided, returns its values re-indexed with numeric keys.
 * @return array<int, mixed>        array containing values that exist in all input arrays, re-indexed
 *                                  with sequential numeric keys starting from zero
 */
function intersection(array ...$arrays): array
{
    if ($arrays === []) {
        return [];
    }

    if (count($arrays) === 1) {
        return array_values($arrays[0]);
    }

    $result = array_intersect(...$arrays);

    return array_values($result);
}
