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
 * Tests whether at least one element in an iterable satisfies the provided predicate.
 *
 * Returns a closure that accepts an iterable and returns true if any element
 * passes the predicate test, false otherwise. Short-circuits on the first
 * matching element for optimal performance. This is a curried function enabling
 * partial application in functional pipelines.
 *
 * ```php
 * $hasEven = any(fn($n) => $n % 2 === 0);
 * $hasEven([1, 3, 4, 5]); // true
 * $hasEven([1, 3, 5]); // false
 *
 * // Use in pipelines for validation
 * $hasInvalidEmail = any(fn($user) => !filter_var($user['email'], FILTER_VALIDATE_EMAIL));
 * $hasInvalidEmail($users); // true if any user has invalid email
 * ```
 *
 * @param  callable(mixed): bool   $c Predicate function that tests each element. Receives a single
 *                                    value and returns a boolean indicating whether the element
 *                                    satisfies the test condition. First truthy result halts iteration.
 * @return Closure(iterable): bool Returns a closure accepting an iterable and returning a boolean
 *                                 indicating whether any element satisfies the predicate. Iteration
 *                                 stops at the first match for efficiency.
 *
 * @see anyWithKeys() For testing with key access
 * @see all() For testing if all elements satisfy the predicate
 */
function any(callable $c): Closure
{
    return static function (iterable $it) use ($c): bool {
        foreach ($it as $v) {
            if ($c($v)) {
                return true;
            }
        }

        return false;
    };
}
