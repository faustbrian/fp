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
 * Creates a greater-than-or-equal comparison predicate for filtering and validation.
 *
 * Returns a curried function that tests whether a given numeric value is greater than
 * or equal to the specified threshold. Useful for inclusive range filtering and
 * validation scenarios where the boundary value should be included.
 *
 * ```php
 * $ages = [16, 18, 21, 25, 30];
 * $isAdult = gte(18);
 *
 * $adults = array_filter($ages, $isAdult); // [18, 21, 25, 30]
 * ```
 *
 * @param  float|int                $value The threshold value to compare against (inclusive)
 * @return Closure(float|int): bool Returns a predicate function that returns true
 *                                  if the input is greater than or equal to the threshold
 */
function gte(int|float $value): Closure
{
    return static fn (int|float $v): bool => $v >= $value;
}
