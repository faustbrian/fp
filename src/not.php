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
 * Creates a negated version of a predicate function.
 *
 * Returns a new function that inverts the boolean result of the original
 * predicate. This is useful for filtering operations where you want to
 * exclude items that match a condition rather than include them.
 *
 * ```php
 * $isEven = fn($n) => $n % 2 === 0;
 * $isOdd = not($isEven);
 *
 * $isOdd(3); // true
 * $isOdd(4); // false
 *
 * // Filtering with negation
 * $numbers = [1, 2, 3, 4, 5];
 * $odds = array_filter($numbers, not($isEven)); // [1, 3, 5]
 * ```
 *
 * @param  callable $predicate The predicate function to negate. Can accept any number
 *                             and type of arguments, which will be forwarded to the
 *                             returned closure unchanged.
 * @return Closure  A new function that returns the opposite boolean value of the
 *                  original predicate when called with the same arguments
 */
function not(callable $predicate): Closure
{
    return static fn (...$args): bool => !$predicate(...$args);
}
