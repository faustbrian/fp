<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_merge;
use function array_unique;
use function array_values;

/**
 * Combines multiple arrays and returns all unique values.
 *
 * Merges all provided arrays and removes duplicate values, returning a new
 * array containing only unique elements across all inputs. The result is
 * re-indexed with sequential numeric keys starting from 0, regardless of
 * the original array structures.
 *
 * Uses strict comparison (===) to determine uniqueness, so different types
 * with the same value are considered distinct (e.g., 1 and '1' are different).
 *
 * ```php
 * union([1, 2], [2, 3], [3, 4]); // Returns: [1, 2, 3, 4]
 * union(['a', 'b'], ['b', 'c']); // Returns: ['a', 'b', 'c']
 * union([1, '1'], [1, 2]); // Returns: [1, '1', 2] (types matter)
 * union(); // Returns: []
 *
 * // Combining user groups
 * $allUsers = union($admins, $editors, $viewers);
 * ```
 *
 * @param  array<mixed>      ...$arrays Variable number of arrays to combine. Each array can
 *                                      contain values of any type. Empty arrays are allowed
 *                                      and will be ignored. When no arrays are provided,
 *                                      returns an empty array.
 * @return array<int, mixed> A new array containing all unique values from all input arrays.
 *                           Values are re-indexed with sequential numeric keys (0, 1, 2, ...).
 *                           Original keys are not preserved.
 */
function union(array ...$arrays): array
{
    if ($arrays === []) {
        return [];
    }

    $merged = array_merge(...$arrays);

    return array_values(array_unique($merged));
}
