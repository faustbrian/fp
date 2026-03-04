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
 * Creates a greater-than comparison predicate for filtering and validation.
 *
 * Returns a curried function that tests whether a given numeric value is strictly
 * greater than the specified threshold. Commonly used with array_filter, first,
 * or other functional operations that accept predicates.
 *
 * ```php
 * $numbers = [5, 10, 15, 20, 25];
 * $greaterThan10 = gt(10);
 *
 * $filtered = array_filter($numbers, $greaterThan10); // [15, 20, 25]
 * ```
 *
 * @param  float|int                $value The threshold value to compare against
 * @return Closure(float|int): bool Returns a predicate function that returns true
 *                                  if the input is greater than the threshold
 */
function gt(int|float $value): Closure
{
    return static fn (int|float $v): bool => $v > $value;
}
