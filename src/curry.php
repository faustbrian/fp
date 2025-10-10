<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;
use ReflectionFunction;

use function count;

/**
 * Transforms a function to allow partial application through currying.
 *
 * Currying converts a multi-argument function into a sequence of single-argument
 * functions. When called with fewer arguments than required, it returns a new
 * function that accepts the remaining arguments. This enables powerful composition
 * patterns and reduces the need for anonymous wrapper functions.
 *
 * ```php
 * $add = function ($a, $b, $c) { return $a + $b + $c; };
 * $curriedAdd = curry($add);
 *
 * $add5 = $curriedAdd(5);
 * $add5and3 = $add5(3);
 * echo $add5and3(2); // 10
 * ```
 *
 * @param  callable $fn    The function to curry, which will be transformed to accept
 *                         partial application. Can be any callable including closures,
 *                         named functions, or method references.
 * @param  null|int $arity The number of required arguments for the function. When null,
 *                         uses reflection to auto-detect the number of required parameters.
 *                         Useful for overriding detection for variadic functions.
 * @return Closure  a curried version that returns intermediate closures when called
 *                  with insufficient arguments, or executes the original function
 *                  when all required arguments are provided
 */
function curry(callable $fn, ?int $arity = null): Closure
{
    if ($arity === null) {
        $reflection = new ReflectionFunction($fn instanceof Closure ? $fn : Closure::fromCallable($fn));
        $arity = $reflection->getNumberOfRequiredParameters();
    }

    return static function (...$args) use ($fn, $arity): mixed {
        if (count($args) >= $arity) {
            return $fn(...$args);
        }

        return curry(static fn (...$rest) => $fn(...$args, ...$rest), $arity - count($args));
    };
}
