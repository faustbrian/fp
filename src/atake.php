<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function array_slice;
use function is_array;

/**
 * Takes the first N elements from an iterable and returns them as an array.
 *
 * Returns a closure that extracts a limited number of elements from the
 * beginning of any iterable. For arrays, uses efficient array_slice() for
 * O(n) performance. For other iterables like generators, manually iterates
 * until the limit is reached. This is analogous to SQL's LIMIT clause but
 * works with any iterable. Preserves keys from the original iterable in the
 * result. This is a curried function for use in functional pipelines.
 *
 * ```php
 * $take3 = atake(3);
 * $take3([1, 2, 3, 4, 5]); // [1, 2, 3]
 * $take3(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]); // ['a' => 1, 'b' => 2, 'c' => 3]
 *
 * // Limit results from a generator
 * $numbers = function() { foreach (range(1, 1000) as $n) yield $n; };
 * $first10 = atake(10);
 * $first10($numbers()); // [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
 * ```
 *
 * @see adrop() For skipping N elements and taking the rest
 * @see slice() For taking a slice from an arbitrary position
 * @see head() For taking just the first element
 * @param  int                      $count Maximum number of elements to take from the iterable.
 *                                         Must be non-negative. A value of 0 returns an empty array.
 *                                         Negative values will result in undefined behavior.
 * @return Closure(iterable): array Returns a closure accepting an iterable and returning an array
 *                                  containing up to $count elements with their original keys preserved.
 *                                  If the iterable has fewer than $count elements, all elements are
 *                                  returned.
 */
function atake(int $count): Closure
{
    return static function (iterable $a) use ($count): array {
        if (is_array($a)) {
            return array_slice($a, 0, $count);
        }

        $ret = [];

        foreach ($a as $k => $v) {
            if (--$count < 0) {
                break;
            }

            $ret[$k] = $v;
        }

        return $ret;
    };
}
