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
 * Applies multiple functions to the same argument and returns array of results.
 *
 * Takes multiple functions and returns a new function that applies all of
 * them to its argument, collecting the results in an array. Useful for
 * applying different transformations or extractors to the same data.
 *
 * ```php
 * $stats = juxt(
 *     fn($arr) => count($arr),
 *     fn($arr) => array_sum($arr),
 *     fn($arr) => array_sum($arr) / count($arr)
 * );
 * $stats([1, 2, 3, 4]); // [4, 10, 2.5]
 *
 * $userInfo = juxt(
 *     fn($u) => $u['name'],
 *     fn($u) => $u['email'],
 *     fn($u) => $u['age']
 * );
 * $userInfo(['name' => 'Alice', 'email' => 'a@example.com', 'age' => 25]);
 * // ['Alice', 'a@example.com', 25]
 * ```
 *
 * @param  callable ...$fns Variable number of functions to apply
 * @return Closure  Function accepting value and returning array of results
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
