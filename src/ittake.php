<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;
use Iterator;
use LimitIterator;

use function array_slice;
use function is_array;

/**
 * Returns a higher-order function that yields a limited number of values from an iterable.
 *
 * This function creates a curried operation that lazily extracts the first N elements
 * from an iterable, similar to an SQL LIMIT clause. The implementation optimizes for
 * performance by using array_slice() for arrays and LimitIterator for Iterator objects.
 * Keys are preserved from the original iterable.
 *
 * The lazy evaluation means that for generators or large iterables, only the requested
 * number of items will be processed, providing memory and performance benefits when
 * working with large or infinite sequences.
 *
 * ```php
 * // Take first 3 elements
 * $takeThree = ittake(3);
 * $result = $takeThree([1, 2, 3, 4, 5]); // [0 => 1, 1 => 2, 2 => 3]
 *
 * // Limit infinite sequence
 * $powers = iterate(1, fn($n) => $n * 2);
 * $firstFive = ittake(5);
 * $result = [...$firstFive($powers)]; // [1, 2, 4, 8, 16]
 *
 * // Combine with other operations
 * $transform = pipe(
 *     itfilter(fn($n) => $n % 2 === 0),
 *     ittake(10)
 * );
 * ```
 *
 * @see iterate() For generating infinite sequences that can be limited with ittake().
 * @param  int                               $count The maximum number of items to yield from the iterable. Must be
 *                                                  a non-negative integer. If count is 0, yields nothing. If count
 *                                                  exceeds the iterable size, yields all available items.
 * @return Closure(array|Iterator): iterable A curried function that accepts an array or Iterator
 *                                           and returns an iterable yielding at most $count items.
 *                                           Preserves keys from the original iterable.
 */
function ittake(int $count): Closure
{
    return static function (array|Iterator $a) use ($count): iterable {
        // No idea if this is faster than manually foreach()ing, but it's slicker.
        yield from is_array($a)
            ? array_slice($a, 0, $count)
            : new LimitIterator($a, 0, $count);
    };
}
