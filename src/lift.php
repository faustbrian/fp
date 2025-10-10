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
 * Takes a regular function and returns a function that works on wrapped
 * values (arrays). This is the fundamental lifting operation that transforms
 * normal functions to work within applicative contexts.
 *
 * Equivalent to map for unary functions, but makes the applicative
 * pattern explicit.
 *
 * ```php
 * $add1 = fn($x) => $x + 1;
 * $liftedAdd1 = lift($add1);
 *
 * $liftedAdd1([1, 2, 3]); // [2, 3, 4]
 * ```
 *
 * @param  callable                 $fn Unary function to lift
 * @return Closure(iterable): array Function accepting iterable and returning mapped array
 *
 * @see map() For the underlying implementation
 * @see liftA2() For lifting binary functions
 */
function lift(callable $fn): Closure
{
    return map($fn);
}
