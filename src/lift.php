<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function Cline\fp\map;

/**
 * Lifts a unary function into an applicative context.
 *
 * Takes a regular function and returns a function that works on wrapped values (arrays).
 * This is the fundamental lifting operation that transforms normal functions to work
 * within applicative contexts, enabling functional composition patterns.
 *
 * Functionally equivalent to map() for unary functions, but makes the applicative
 * pattern explicit in the code. The lifted function will apply the original function
 * to each element in the provided iterable, preserving keys.
 *
 * ```php
 * // Lift a simple function
 * $add1 = fn($x) => $x + 1;
 * $liftedAdd1 = lift($add1);
 * $liftedAdd1([1, 2, 3]); // [2, 3, 4]
 *
 * // Lift built-in functions
 * $upper = lift('strtoupper');
 * $upper(['foo', 'bar']); // ['FOO', 'BAR']
 *
 * // Use in functional pipelines
 * $transform = pipe(
 *     lift(fn($x) => $x * 2),
 *     lift(fn($x) => $x + 10)
 * );
 * $transform([1, 2, 3]); // [12, 14, 16]
 * ```
 *
 * @param  callable(mixed): mixed   $fn A unary function (single argument) to lift into the
 *                                      applicative context. The function will be applied to
 *                                      each element of the iterable.
 * @return Closure(iterable): array a curried function that accepts an iterable and returns
 *                                  an array with the function applied to each element
 *
 * @see map() For the underlying implementation and detailed behavior.
 * @see liftA2() For lifting binary functions into applicative contexts.
 * @see liftA3() For lifting ternary functions into applicative contexts.
 */
function lift(callable $fn): Closure
{
    return map($fn);
}
