<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function is_array;
use function iterator_to_array;
use function uasort;

/**
 * Sorts an iterable using a custom comparator function in a pipeable manner.
 *
 * Returns a closure that sorts elements according to the provided comparator function,
 * which determines the relative ordering of any two elements. The comparator should
 * return a negative number, zero, or positive number to indicate element ordering.
 *
 * Preserves array keys during sorting using uasort. This provides full control over
 * sort logic for complex comparisons, multi-field sorting, or custom ordering rules
 * that cannot be expressed with simple key extraction.
 *
 * ```php
 * $sorted = pipe(
 *     [3, 1, 4, 1, 5],
 *     sortWith(fn($a, $b) => $a <=> $b)
 * ); // [1 => 1, 3 => 1, 0 => 3, 2 => 4, 4 => 5]
 *
 * $descending = pipe(
 *     ['a' => 10, 'b' => 5, 'c' => 15],
 *     sortWith(fn($a, $b) => $b <=> $a)
 * ); // ['c' => 15, 'a' => 10, 'b' => 5]
 * ```
 *
 * @see sortBy() For sorting by a key extraction function
 * @param  callable $comparator Comparison function with signature (mixed $a, mixed $b): int that
 *                              returns a negative number if $a < $b, zero if $a == $b, or a positive
 *                              number if $a > $b. Determines the relative ordering of elements.
 * @return Closure  A closure with signature (iterable $it): array that sorts the iterable
 *                  using the comparator function and returns the sorted array with preserved keys
 */
function sortWith(callable $comparator): Closure
{
    return static function (iterable $it) use ($comparator): array {
        $result = is_array($it) ? $it : iterator_to_array($it);
        uasort($result, $comparator);

        return $result;
    };
}
