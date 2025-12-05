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
 * Creates a predicate function that tests if a value falls within a specified range.
 *
 * Returns a closure that checks whether a given numeric value is between the
 * minimum and maximum values (inclusive on both ends). This is a curried
 * function enabling partial application in functional pipelines, filters, and
 * validation logic. Both bounds are inclusive, meaning values equal to min or
 * max will return true.
 *
 * ```php
 * $inRange = between(10, 20);
 * $inRange(15); // true
 * $inRange(25); // false
 * $inRange(10); // true (inclusive)
 * $inRange(20); // true (inclusive)
 *
 * // Filter arrays
 * $scores = [5, 15, 25, 35];
 * array_filter($scores, between(10, 30)); // [15, 25]
 *
 * // Validate user input
 * $isValidAge = between(18, 120);
 * $isValidAge($userAge); // true if adult age range
 * ```
 *
 * @param  float|int $min Lower bound of the range (inclusive). Values equal to or
 *                        greater than this are considered within range.
 * @param  float|int $max Upper bound of the range (inclusive). Values equal to or
 *                        less than this are considered within range.
 * @return Closure   Returns a closure accepting a numeric value and returning true
 *                   if the value is within the range [min, max], false otherwise.
 *                   The test is inclusive on both ends.
 */
function between(int|float $min, int|float $max): Closure
{
    return static fn (int|float $v): bool => $v >= $min && $v <= $max;
}
