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
 * Composes multiple functions into a single function pipeline.
 *
 * Returns a closure that applies a series of functions in left-to-right order,
 * passing the output of each function as input to the next. This creates a
 * function pipeline where data flows sequentially through each transformation.
 * The composition is variadic, accepting any number of callable arguments.
 * This is the fundamental building block for creating complex data transformation
 * pipelines in a readable, declarative style.
 *
 * ```php
 * $addOne = fn($x) => $x + 1;
 * $double = fn($x) => $x * 2;
 * $addThenDouble = compose($addOne, $double);
 * $addThenDouble(5); // (5 + 1) * 2 = 12
 *
 * // String transformation pipeline
 * $slugify = compose(
 *     fn($x) => trim($x),
 *     fn($x) => strtolower($x),
 *     fn($x) => str_replace(' ', '-', $x)
 * );
 * $slugify('  Hello World  '); // 'hello-world'
 *
 * // Complex data pipeline
 * $processUsers = compose(
 *     filter(fn($u) => $u['active']),
 *     map(fn($u) => $u['email']),
 *     collect
 * );
 * $processUsers($users); // Array of active user emails
 * ```
 *
 * @param  callable ...$fns Variable number of callables to compose into a pipeline.
 *                          Functions are applied left-to-right, with each receiving
 *                          the output of the previous function as its input. The
 *                          return type of each function must be compatible with the
 *                          parameter type of the next function.
 * @return Closure  Returns a closure accepting an initial value and returning the
 *                  result after passing it through all composed functions sequentially.
 *                  The closure's return type matches the return type of the final
 *                  function in the composition.
 */
function compose(callable ...$fns): Closure
{
    return static function (mixed $arg) use ($fns): mixed {
        foreach ($fns as $fn) {
            $arg = $fn($arg);
        }

        return $arg;
    };
}
