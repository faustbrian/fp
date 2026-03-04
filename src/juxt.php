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
 * Returns a higher-order function that applies multiple functions to the same argument.
 *
 * Takes multiple functions and returns a new function that applies all of them to its
 * argument, collecting the results in an indexed array. Each function receives the same
 * input value, and their outputs are combined in the order the functions were provided.
 *
 * This is particularly useful for extracting multiple pieces of information from the same
 * data source, computing different metrics simultaneously, or applying parallel transformations
 * without repeating the input data.
 *
 * ```php
 * // Calculate multiple statistics
 * $stats = juxt(
 *     fn($arr) => count($arr),
 *     fn($arr) => array_sum($arr),
 *     fn($arr) => array_sum($arr) / count($arr)
 * );
 * $stats([1, 2, 3, 4]); // [4, 10, 2.5]
 *
 * // Extract multiple fields
 * $userInfo = juxt(
 *     fn($u) => $u['name'],
 *     fn($u) => $u['email'],
 *     fn($u) => $u['age']
 * );
 * $userInfo(['name' => 'Alice', 'email' => 'a@example.com', 'age' => 25]);
 * // ['Alice', 'a@example.com', 25]
 *
 * // Parallel transformations
 * $transforms = juxt(
 *     'strtoupper',
 *     'strtolower',
 *     fn($s) => str_reverse($s)
 * );
 * $transforms('Hello'); // ['HELLO', 'hello', 'olleH']
 * ```
 *
 * @param  callable(mixed): mixed ...$fns Variable number of functions to apply to the input value.
 *                                        Each function receives the same argument and can return
 *                                        any type. Functions are applied in the order provided.
 * @return Closure(mixed): array  a function that accepts a value and returns an indexed array
 *                                containing the results of applying each function to that value
 */
function juxt(callable ...$fns): Closure
{
    return static function (mixed $value) use ($fns): array {
        $results = [];

        foreach ($fns as $fn) {
            $results[] = $fn($value);
        }

        return $results;
    };
}
