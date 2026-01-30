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
 * Tests whether all elements in an iterable satisfy a predicate.
 *
 * Returns a curried function that checks if every element in the provided iterable
 * passes the predicate test. Only values are passed to the predicate, making it
 * compatible with PHP's internal single-argument functions. Returns true if all
 * elements satisfy the condition, false otherwise. Short-circuits on the first
 * failure for performance optimization.
 *
 * ```php
 * // Check if all numbers are positive
 * $allPositive = all(fn($v) => $v > 0);
 * $result = $allPositive([1, 2, 3, 4]); // true
 * $result = $allPositive([1, -2, 3]); // false
 *
 * // Using internal PHP function
 * $allNumeric = all('is_numeric');
 * $result = $allNumeric(['10', '20', '30']); // true
 * $result = $allNumeric(['10', 'abc', '30']); // false
 * ```
 *
 * @see allWithKeys() For testing with key access
 * @see any() For testing if any element satisfies the predicate
 * @param  callable(mixed): bool   $c Predicate function receiving (mixed $value): bool that tests
 *                                    each element. Returns true to indicate the element passes
 *                                    validation, false otherwise. Accepts internal PHP functions
 *                                    like 'is_numeric', 'is_string', etc.
 * @return Closure(iterable): bool Function accepting an iterable and returning true if all elements
 *                                 satisfy the predicate, false if any element fails. Short-circuits
 *                                 on the first failure to avoid unnecessary iterations.
 */
function all(callable $c): Closure
{
    return static function (iterable $it) use ($c): bool {
        foreach ($it as $v) {
            if (!$c($v)) {
                return false;
            }
        }

        return true;
    };
}
