<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_reverse;

/**
 * Reverses the order of elements in an array.
 *
 * Returns a new array with elements in reverse order. Keys are preserved,
 * maintaining the association between keys and values even after reversal.
 *
 * ```php
 * reverse([1, 2, 3, 4]); // [3 => 4, 2 => 3, 1 => 2, 0 => 1]
 * reverse(['a' => 1, 'b' => 2]); // ['b' => 2, 'a' => 1]
 * reverse([]); // []
 * ```
 *
 * @param  array<mixed> $a The array to reverse
 * @return array<mixed> New array with elements in reverse order
 */
function reverse(array $a): array
{
    return array_reverse($a, true);
}
