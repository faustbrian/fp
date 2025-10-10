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
 * Creates a function that takes elements from an iterable while a predicate returns true.
 *
 * Returns a curried function that, when given an iterable, will accumulate
 * elements into an array until the predicate returns false for any element.
 * Once a false condition is encountered, iteration stops immediately and
 * remaining elements are ignored. Preserves original keys from the iterable.
 *
 * ```php
 * $takeWhilePositive = takeWhile(fn($x) => $x > 0);
 * $takeWhilePositive([5, 3, 1, -2, 4]); // Returns: [5, 3, 1]
 * $takeWhilePositive([10, 20, 30]); // Returns: [10, 20, 30]
 *
 * // With associative arrays
 * $takeWhileShort = takeWhile(fn($s) => strlen($s) < 5);
 * $takeWhileShort(['a' => 'hi', 'b' => 'test', 'c' => 'hello']); // Returns: ['a' => 'hi', 'b' => 'test']
 * ```
 *
 * @param  callable(mixed): bool $predicate A function that receives each value and returns
 *                                          a boolean. Iteration continues while this returns
 *                                          true and stops immediately when it returns false.
 * @return Closure               A function that accepts an iterable and returns an array containing
 *                               all elements up to (but not including) the first element where the
 *                               predicate returned false. Original keys are preserved.
 */
function takeWhile(callable $predicate): Closure
{
    return static function (iterable $it) use ($predicate): array {
        $result = [];

        foreach ($it as $k => $v) {
            if (!$predicate($v)) {
                break;
            }

            $result[$k] = $v;
        }

        return $result;
    };
}
