<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

/**
 * Passes a value through a series of functions, left to right.
 *
 * Each function receives the result of the previous function as its argument,
 * creating a pipeline of transformations. This enables functional composition
 * without deep nesting and makes data transformation chains more readable by
 * presenting operations in the order they execute.
 *
 * Unlike compose(), which applies functions right to left, pipe() applies them
 * in the natural reading order (left to right), making it more intuitive for
 * sequential operations.
 *
 * ```php
 * $result = pipe(
 *     10,
 *     fn($x) => $x * 2,      // 20
 *     fn($x) => $x + 5,      // 25
 *     fn($x) => $x / 5       // 5
 * ); // Returns 5
 *
 * $users = pipe(
 *     User::all(),
 *     fn($users) => array_filter($users, fn($u) => $u->active),
 *     fn($users) => array_map(fn($u) => $u->email, $users),
 *     fn($emails) => array_unique($emails)
 * );
 * ```
 *
 * @param  mixed    $arg    the initial value to pass through the pipeline
 * @param  callable ...$fns Variable number of callable functions to apply sequentially.
 *                          Each function receives the result of the previous function.
 * @return mixed    the final result after applying all functions in sequence
 *
 * @api
 */
function pipe(mixed $arg, callable ...$fns): mixed
{
    foreach ($fns as $fn) {
        $arg = $fn($arg);
    }

    return $arg;
}
