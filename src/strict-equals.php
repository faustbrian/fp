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
 * Creates a predicate that performs strict equality comparison against a fixed value.
 *
 * Returns a closure that checks if its input strictly equals the provided reference value
 * using the identity operator (===). This checks both type and value equality without type
 * coercion, making it suitable for filtering, validation, and functional composition patterns.
 *
 * Commonly used with filter operations, conditional logic in pipelines, or anywhere a reusable
 * equality check is needed. The strict comparison ensures type safety and prevents unexpected
 * matches from type coercion.
 *
 * ```php
 * $isActive = pipe(
 *     ['status' => 'active'],
 *     prop('status'),
 *     strictEquals('active')
 * ); // true
 *
 * $numbers = pipe(
 *     [1, 2, '2', 3, 2],
 *     filter(strictEquals(2))
 * ); // [1, 2, 4 => 2] (string '2' excluded)
 * ```
 *
 * @param  mixed   $value Reference value to compare against. Can be any type including null,
 *                        objects, arrays, or scalars. The comparison uses strict identity
 *                        checking without type coercion.
 * @return Closure A closure with signature (mixed $v): bool that returns true if the input
 *                 is strictly identical (===) to the reference value, false otherwise
 */
function strictEquals(mixed $value): Closure
{
    return static fn (mixed $v): bool => $v === $value;
}
