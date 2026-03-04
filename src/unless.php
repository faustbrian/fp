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
 * Conditionally apply a function only when a predicate returns false.
 *
 * Creates a higher-order function that evaluates a condition against a value.
 * When the condition returns false, the transformation function is applied.
 * When the condition returns true, the original value passes through unchanged.
 * This is the inverse of the `when()` function and useful for expressing
 * negative conditional logic in a functional pipeline.
 *
 * ```php
 * $processIfNotEmpty = unless(
 *     fn($str) => empty($str),
 *     fn($str) => strtoupper($str)
 * );
 *
 * $result1 = $processIfNotEmpty('hello'); // 'HELLO' (not empty, so transform)
 * $result2 = $processIfNotEmpty('');      // '' (empty, so pass through)
 * ```
 *
 * @param  callable $condition Predicate function that receives the value and returns
 *                             a boolean. When false, the transformation is applied.
 * @param  callable $fn        Transformation function applied when condition is false.
 *                             Receives the value and returns the transformed result.
 * @return Closure  returns a closure that accepts a value and either transforms
 *                  it (when condition is false) or returns it unchanged (when true)
 */
function unless(callable $condition, callable $fn): Closure
{
    return static fn (mixed $value): mixed => $condition($value) ? $value : $fn($value);
}
