<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_diff;
use function array_values;

/**
 * Computes the set difference between the first array and all subsequent arrays.
 *
 * Returns values from the first array that don't appear in any of the other arrays,
 * using loose comparison (==) for value matching. The result is re-indexed with
 * sequential numeric keys starting from 0, regardless of the original array's keys.
 * Useful for filtering collections, finding unique values, or implementing set operations.
 *
 * ```php
 * difference([1, 2, 3, 4], [2, 4], [5]); // [1, 3]
 * difference(['a', 'b', 'c'], ['b']); // ['a', 'c']
 * ```
 *
 * @param  array<array-key, mixed> $array     The primary array to compare against others.
 *                                            Values from this array that don't exist in
 *                                            subsequent arrays will be returned.
 * @param  array<array-key, mixed> ...$others Zero or more arrays to compare against the
 *                                            primary array. Values appearing in any of
 *                                            these arrays are excluded from the result.
 *                                            When no arrays are provided, returns the
 *                                            primary array with re-indexed keys.
 * @return array<int, mixed>       sequential array containing values from the first array
 *                                 that don't appear in any subsequent arrays, with numeric
 *                                 keys starting from 0
 */
function difference(array $array, array ...$others): array
{
    if ($others === []) {
        return array_values($array);
    }

    $result = array_diff($array, ...$others);

    return array_values($result);
}
