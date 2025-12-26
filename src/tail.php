<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_slice;

/**
 * Returns all elements of an array except the first one.
 *
 * Creates a new array containing all elements from the original array
 * starting at index 1. If the array is empty or has only one element,
 * returns an empty array. Original array keys are not preserved.
 *
 * ```php
 * tail([1, 2, 3, 4]); // Returns: [2, 3, 4]
 * tail(['a']); // Returns: []
 * tail([]); // Returns: []
 * ```
 *
 * @see head() For getting the first element of an array
 * @param  array<mixed> $a The array to process. Can be empty or contain
 *                         elements of any type. Keys are not preserved.
 * @return array<mixed> A new array containing all elements except the first.
 *                      Returns empty array if input has 0 or 1 elements.
 */
function tail(array $a): array
{
    return array_slice($a, 1);
}
