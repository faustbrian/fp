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
 * Creates a predicate function that performs strict equality comparison.
 *
 * Returns a closure that checks if its argument is strictly equal (===) to the
 * provided value. Particularly useful in functional composition patterns like
 * filtering, mapping, or finding elements that match a specific value. The strict
 * comparison ensures type safety by requiring both value and type to match.
 *
 * ```php
 * $isZero = equals(0);
 * $isZero(0); // true
 * $isZero('0'); // false (strict comparison)
 *
 * $users = [['status' => 'active'], ['status' => 'inactive']];
 * $active = array_filter($users, compose(prop('status'), equals('active')));
 * ```
 *
 * @param  mixed   $value The value to compare against using strict equality (===).
 *                        Can be any type including null, objects, arrays, or scalars.
 * @return Closure a predicate function that accepts a value and returns true if
 *                 it strictly equals the captured value, false otherwise
 */
function equals(mixed $value): Closure
{
    return static fn (mixed $v): bool => $v === $value;
}
