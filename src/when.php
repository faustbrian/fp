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
 * Conditionally apply a function only when a predicate returns true.
 *
 * Creates a higher-order function that evaluates a condition against a value.
 * When the condition returns true, the transformation function is applied.
 * When the condition returns false, the original value passes through unchanged.
 * This enables declarative conditional transformations in functional pipelines
 * without explicit if statements.
 *
 * ```php
 * $uppercaseIfLong = when(
 *     fn($str) => strlen($str) > 5,
 *     fn($str) => strtoupper($str)
 * );
 *
 * $result1 = $uppercaseIfLong('hello');     // 'hello' (not long enough)
 * $result2 = $uppercaseIfLong('functional'); // 'FUNCTIONAL' (long enough)
 * ```
 *
 * @param  callable(mixed): bool  $condition Predicate function that receives the value and returns
 *                                           a boolean. When true, the transformation is applied.
 * @param  callable(mixed): mixed $fn        Transformation function applied when condition is true.
 *                                           Receives the value and returns the transformed result.
 * @return Closure(mixed): mixed  Returns a closure that accepts a value and either transforms
 *                                it (when condition is true) or returns it unchanged (when false)
 *
 * @since 1.0.0
 */
function when(callable $condition, callable $fn): Closure
{
    return static fn (mixed $value): mixed => $condition($value) ? $fn($value) : $value;
}
