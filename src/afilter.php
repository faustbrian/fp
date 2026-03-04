<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function array_filter;
use function is_array;

/**
 * Filters an iterable based on a predicate function that receives only values.
 *
 * Returns a curried function that accepts an iterable and filters its elements using
 * the provided predicate. Only the value is passed to the predicate function, making
 * it compatible with PHP's internal single-argument functions. Keys are preserved in
 * the filtered result array.
 *
 * ```php
 * // Filter numbers greater than 10
 * $filterLarge = afilter(fn($v) => $v > 10);
 * $result = $filterLarge([5, 20, 8, 15, 3]); // [1 => 20, 3 => 15]
 *
 * // Default behavior: filter truthy values
 * $filterTruthy = afilter();
 * $result = $filterTruthy([0, 1, false, 'text', null]); // [1 => 1, 3 => 'text']
 *
 * // Compatible with internal functions like 'is_numeric'
 * $filterNumeric = afilter('is_numeric');
 * $result = $filterNumeric(['a', '10', 'b', '20']); // [1 => '10', 3 => '20']
 * ```
 *
 * @see afilterWithKeys() For filtering with key access
 * @param  null|callable(mixed): bool $c Predicate function receiving (mixed $value): bool
 *                                       that determines whether each element should be included.
 *                                       When null, filters truthy values. Accepts internal PHP
 *                                       functions like 'is_numeric', 'is_string', etc.
 * @return Closure(iterable): array   Function accepting an iterable and returning a filtered array
 *                                    with preserved keys. The returned closure maintains the original
 *                                    key-value associations from the input iterable.
 */
function afilter(?callable $c = null): Closure
{
    $c ??= static fn (mixed $v, mixed $k = null): bool => (bool) $v;

    return static function (iterable $it) use ($c): array {
        if (is_array($it)) {
            return array_filter($it, $c);
        }

        $result = [];

        foreach ($it as $k => $v) {
            if (!$c($v)) {
                continue;
            }

            $result[$k] = $v;
        }

        return $result;
    };
}
