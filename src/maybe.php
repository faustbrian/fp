<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

/**
 * Wraps a callable in null-safe logic that passes through null values unchanged.
 *
 * Creates a wrapper function that checks if the input is null before executing
 * the wrapped callable. If the input is null, it returns null immediately without
 * calling the function. This prevents null pointer errors and enables safe chaining
 * of operations where intermediate results may be null, particularly useful in
 * functional pipelines and composition patterns.
 *
 * ```php
 * $toUpper = fn($s) => strtoupper($s);
 * $safeUpper = maybe($toUpper);
 *
 * $safeUpper('hello'); // 'HELLO'
 * $safeUpper(null);    // null
 *
 * // Using in a pipeline to safely transform nullable values
 * pipe(
 *     getUserEmail($id),        // might return null
 *     maybe(trim(...)),         // only trims if not null
 *     maybe(strtolower(...)),   // only lowercases if not null
 *     maybe(fn($e) => "Email: $e")
 * ); // Returns null if user has no email, otherwise formatted string
 *
 * // Avoiding null errors in array mapping
 * $ids = [1, 2, null, 3];
 * array_map(maybe(fn($id) => $id * 2), $ids); // [2, 4, null, 6]
 * ```
 *
 * @param  callable $c The function to wrap with null-safety. Should accept
 *                     a single parameter and can return any type. Will only
 *                     be invoked when the input value is not null.
 * @return callable Returns a null-safe wrapper that executes the original
 *                  function only for non-null inputs, passing null through unchanged
 */
function maybe(callable $c): callable
{
    return static fn (mixed $val): mixed => null === $val ? null : $c($val);
}
