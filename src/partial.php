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
 * Creates a partially applied function by fixing initial arguments.
 *
 * Returns a new function with the specified arguments pre-applied to the left side.
 * When the returned function is called, it receives additional arguments which are
 * appended after the pre-applied ones. This enables creating specialized functions
 * from more general ones, supporting currying patterns and dependency injection in
 * functional pipelines.
 *
 * ```php
 * $add = fn($a, $b, $c) => $a + $b + $c;
 * $addFive = partial($add, 5);
 * $addFive(10, 3); // 18 (5 + 10 + 3)
 *
 * $greet = fn($greeting, $name) => "$greeting, $name!";
 * $sayHello = partial($greet, 'Hello');
 * $sayHello('Alice'); // 'Hello, Alice!'
 * $sayHello('Bob'); // 'Hello, Bob!'
 *
 * // In pipelines
 * $userGreetings = pipe(
 *     $names,
 *     map(partial($greet, 'Welcome'))
 * );
 * ```
 *
 * @param  callable $fn      The function to partially apply. Will receive the pre-applied
 *                           arguments followed by any arguments passed to the returned closure.
 * @param  mixed    ...$args The arguments to pre-apply to the function. These will be
 *                           positioned at the start of the argument list, with additional
 *                           arguments appended when the returned function is invoked.
 * @return Closure  A new function that accepts additional arguments and combines them
 *                  with the pre-applied arguments before calling the original function
 */
function partial(callable $fn, mixed ...$args): Closure
{
    return static fn (...$rest) => $fn(...$args, ...$rest);
}
