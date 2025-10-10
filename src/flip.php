<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function func_num_args;

/**
 * Reverses the order of arguments for a binary function.
 *
 * Takes a function expecting two arguments and returns a new function
 * that accepts the same arguments in reverse order. The returned function
 * supports partial application - call it with one argument to get a function
 * waiting for the second argument.
 *
 * ```php
 * $subtract = fn($a, $b) => $a - $b;
 * $flippedSubtract = flip($subtract);
 * $subtract(10, 3); // 7
 * $flippedSubtract(10, 3); // -7 (same as subtract(3, 10))
 *
 * $divide = fn($a, $b) => $a / $b;
 * $divideBy2 = flip($divide)(2);
 * $divideBy2(10); // 5 (same as divide(10, 2))
 * ```
 *
 * @param  callable $fn The binary function to flip
 * @return Closure  Curried function accepting arguments in reverse order
 */
function flip(callable $fn): Closure
{
    return static function (mixed $arg0, mixed $arg1 = null) use ($fn): mixed {
        if (1 === func_num_args()) {
            // Partially applied: fix second arg of original, wait for first arg
            // This enables "partial application from right"
            return static fn (mixed $arg1): mixed => $fn($arg1, $arg0);
        }

        // Fully applied with both args
        // flipped(arg0, arg1) => original(arg1, arg0)
        return $fn($arg1, $arg0);
    };
}
