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

/**
 * Returns all elements of an array except the last one.
 *
 * Opposite of tail() which removes the first element. Creates a new array
 * containing all elements from the original array except the final element.
 * If the array is empty or has only one element, returns an empty array.
 * Original array keys are not preserved - numeric keys are reindexed.
 *
 * ```php
 * init([1, 2, 3, 4]); // [1, 2, 3]
 * init(['a']); // []
 * init([]); // []
 * ```
 *
 * @param  array<mixed> $a The array to process
 * @return array<mixed> Array containing all elements except the last
 */
function init(array $a): array
{
    if ([] === $a) {
        return [];
    }

    return array_values(array_slice($a, 0, -1));
}
