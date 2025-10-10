<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_merge;

/**
 * Concatenates multiple arrays into a single array.
 *
 * Merges two or more arrays into one, preserving all values. Numeric keys
 * are reindexed sequentially, while string keys are preserved. Later arrays
 * overwrite earlier arrays for duplicate string keys.
 *
 * ```php
 * concat([1, 2], [3, 4]); // [1, 2, 3, 4]
 * concat(['a' => 1], ['b' => 2], ['c' => 3]); // ['a' => 1, 'b' => 2, 'c' => 3]
 * concat([1, 2], []); // [1, 2]
 * ```
 *
 * @param  array<mixed> ...$arrays Variable number of arrays to concatenate
 * @return array<mixed> Merged array containing all elements
 */
function concat(array ...$arrays): array
{
    return array_merge(...$arrays);
}
