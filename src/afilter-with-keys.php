<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use const ARRAY_FILTER_USE_BOTH;

use function array_filter;
use function is_array;

/**
 * Filters an iterable based on a predicate function that receives both values and keys.
 *
 * Returns a curried function that accepts an iterable and filters its elements using
 * the provided predicate. The predicate receives both the value and key for each element,
 * allowing key-based filtering logic. Keys are preserved in the filtered result array.
 *
 * This is a separate function from afilter() because PHP's internal functions no longer
 * accept extra arguments, while user-defined functions do. A combined function would be
 * incompatible with single-argument internal array functions like array_filter().
 *
 * ```php
 * // Filter array elements where value is greater than 10 and key is even
 * $filterLargeEvenIndexed = afilterWithKeys(fn($v, $k) => $v > 10 && $k % 2 === 0);
 * $result = $filterLargeEvenIndexed([5, 20, 15, 30, 25]); // [1 => 20, 3 => 30]
 *
 * // Default behavior: filter truthy values with keys preserved
 * $filterTruthy = afilterWithKeys();
 * $result = $filterTruthy(['a' => 0, 'b' => 1, 'c' => '', 'd' => 'text']); // ['b' => 1, 'd' => 'text']
 * ```
 *
 * @see afilter() For filtering without key access
 * @param  null|callable(mixed, mixed): bool $c Predicate function receiving (mixed $value, mixed $key): bool
 *                                              that determines whether each element should be included
 *                                              in the filtered result. When null, filters truthy values.
 * @return Closure(iterable): array          Function accepting an iterable and returning a filtered array
 *                                           with preserved keys. The returned closure maintains the original
 *                                           key-value associations from the input iterable.
 */
function afilterWithKeys(?callable $c = null): Closure
{
    $c ??= static fn (mixed $v, mixed $k = null): bool => (bool) $v;

    return static function (iterable $it) use ($c): array {
        if (is_array($it)) {
            return array_filter($it, $c, ARRAY_FILTER_USE_BOTH);
        }

        $result = [];

        foreach ($it as $k => $v) {
            if (!$c($v, $k)) {
                continue;
            }

            $result[$k] = $v;
        }

        return $result;
    };
}
