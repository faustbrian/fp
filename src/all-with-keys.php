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
 * Tests whether all elements in an iterable satisfy a predicate with key access.
 *
 * Returns a curried function that checks if every element in the provided iterable
 * passes the predicate test. The predicate receives both the value and key for each
 * element. Returns true if all elements satisfy the condition, false otherwise.
 * Short-circuits on the first failure for performance optimization.
 *
 * ```php
 * // Check if all values are positive and keys are numeric
 * $allValidEntries = allWithKeys(fn($v, $k) => is_numeric($k) && $v > 0);
 * $result = $allValidEntries([1, 2, 3]); // true
 * $result = $allValidEntries([1, -2, 3]); // false
 *
 * // Validate associative array structure
 * $allValidUsers = allWithKeys(fn($v, $k) => is_string($k) && isset($v['name'], $v['email']));
 * $result = $allValidUsers(['user1' => ['name' => 'John', 'email' => 'john@example.com']]); // true
 * ```
 *
 * @see all() For testing without key access
 * @see anyWithKeys() For testing if any element satisfies the predicate
 * @param  callable(mixed, mixed): bool $c Predicate function receiving (mixed $value, mixed $key): bool
 *                                         that tests each element. Returns true to indicate the element
 *                                         passes validation, false otherwise.
 * @return Closure(iterable): bool      Function accepting an iterable and returning true if all elements
 *                                      satisfy the predicate, false if any element fails. Short-circuits
 *                                      on the first failure to avoid unnecessary iterations.
 */
function allWithKeys(callable $c): Closure
{
    return static function (iterable $it) use ($c): bool {
        foreach ($it as $k => $v) {
            if (!$c($v, $k)) {
                return false;
            }
        }

        return true;
    };
}
