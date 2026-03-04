<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

/**
 * Creates a function that finds the first element matching a predicate with key access.
 *
 * Returns a curried function that iterates through an iterable, applying the provided
 * predicate to each value and its key. Returns the first element where the predicate
 * returns true, or null if no match is found. Unlike firstValueWithKeys (which returns
 * the predicate's result), this returns the matching element itself. Useful for finding
 * elements based on both value and position, implementing key-sensitive searches, or
 * filtering with index awareness.
 *
 * ```php
 * $findFirstOdd = firstWithKeys(fn($val, $key) => $val % 2 !== 0);
 * $findFirstOdd([2, 4, 7, 9]); // 7
 *
 * $findByKeyPattern = firstWithKeys(fn($val, $key) => str_starts_with($key, 'user_'));
 * $findByKeyPattern($data); // Returns first value with key starting with 'user_'
 * ```
 *
 * @param  callable(mixed, array-key): bool $c Predicate function that receives both the element value and
 *                                             its key from the iterable. Should return true to return the
 *                                             current element and stop iteration, or false to continue
 *                                             searching through remaining elements.
 * @return Closure(iterable): mixed         Curried function that accepts an iterable and returns the first element
 *                                          where the predicate returns true, or null if no matches were found
 *
 * @since 1.0.0
 */
function firstWithKeys(callable $c): Closure
{
    return static function (iterable $it) use ($c): mixed {
        foreach ($it as $k => $v) {
            if ($c($v, $k)) {
                return $v;
            }
        }

        return null;
    };
}
