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
 * Creates a comparison predicate that tests if a value is less than or equal to the specified threshold.
 *
 * Returns a closure that accepts a numeric value and determines whether it is
 * less than or equal to the threshold. Useful for filtering, validation, and
 * functional composition patterns where inclusive comparison logic needs to be
 * passed as a callable.
 *
 * ```php
 * $isAtMost10 = lte(10);
 * $isAtMost10(5);  // true
 * $isAtMost10(10); // true
 * $isAtMost10(15); // false
 *
 * // Using in array filtering
 * $numbers = [3, 7, 10, 15, 20];
 * $atMost10 = array_filter($numbers, lte(10)); // [3, 7, 10]
 * ```
 *
 * @param  float|int $value The threshold value to compare against
 * @return Closure   Returns a closure that accepts an int|float and returns
 *                   true if the input is less than or equal to the threshold
 */
function lte(int|float $value): Closure
{
    return static fn (int|float $v): bool => $v <= $value;
}
