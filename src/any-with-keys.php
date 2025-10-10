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
 * Tests whether any element in an iterable satisfies a predicate with key access.
 *
 * Returns a curried function that checks if at least one element in the provided
 * iterable passes the predicate test. The predicate receives both the value and key
 * for each element. Returns true if any element satisfies the condition, false if
 * none do. Short-circuits on the first match for performance optimization.
 *
 * ```php
 * // Check if any element has a specific key
 * $hasAdminKey = anyWithKeys(fn($v, $k) => $k === 'admin');
 * $result = $hasAdminKey(['user' => true, 'admin' => false]); // true
 * $result = $hasAdminKey(['user' => true, 'guest' => false]); // false
 *
 * // Find if any value-key pair matches criteria
 * $hasHighValue = anyWithKeys(fn($v, $k) => is_numeric($k) && $v > 100);
 * $result = $hasHighValue([50, 75, 150]); // true (index 2, value 150)
 * $result = $hasHighValue([50, 75, 90]); // false
 * ```
 *
 * @param  callable(mixed, mixed): bool $c Predicate function receiving (mixed $value, mixed $key): bool
 *                                         that tests each element. Returns true to indicate the element
 *                                         satisfies the condition, false otherwise.
 * @return Closure(iterable): bool      Function accepting an iterable and returning true if at least one
 *                                      element satisfies the predicate, false if none do. Short-circuits
 *                                      on the first match to avoid unnecessary iterations.
 *
 * @see any() For testing without key access
 * @see allWithKeys() For testing if all elements satisfy the predicate
 */
function anyWithKeys(callable $c): Closure
{
    return static function (iterable $it) use ($c): bool {
        foreach ($it as $k => $v) {
            if ($c($v, $k)) {
                return true;
            }
        }

        return false;
    };
}
