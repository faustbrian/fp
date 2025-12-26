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
 * Creates a function that applies a callable to an array of arguments.
 *
 * Returns a curried function that spreads an array as arguments to the
 * provided callable. Useful for converting array data into function calls
 * or working with variadic functions in a point-free style.
 *
 * ```php
 * $sum = fn(...$nums) => array_sum($nums);
 * $applySum = apply($sum);
 * $applySum([1, 2, 3, 4]); // 10
 *
 * $max = apply('max');
 * $max([5, 2, 8, 1]); // 8
 *
 * // In pipelines
 * $result = pipe(
 *     [[1, 2], [3, 4]],
 *     map(apply(fn($a, $b) => $a + $b))
 * ); // [3, 7]
 * ```
 *
 * @see unapply() For the inverse operation (collecting arguments into an array)
 * @see partial() For partial application of functions
 * @param  callable(mixed...): mixed $fn The callable to apply arguments to. Can be any callable
 *                                       including closures, internal functions, or method references.
 *                                       The callable should accept the number of arguments present
 *                                       in the array passed to the returned closure.
 * @return Closure(array): mixed     Function accepting an array and spreading it as arguments to the
 *                                   callable. The array values become positional arguments in the order
 *                                   they appear. Keys are ignored during spreading.
 */
function apply(callable $fn): Closure
{
    return static fn (array $args): mixed => $fn(...$args);
}
