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
 * Creates a function that returns the first truthy result from applying a callable to iterable values.
 *
 * Returns a curried function that iterates through an iterable, applying the provided
 * callable to each value. Returns the first truthy result produced by the callable,
 * or null if all results are falsy. Unlike filter or find operations that return the
 * matching element, this returns the transformation result itself. Useful for extracting
 * computed values, finding the first successful transformation, or implementing "find and transform" patterns.
 *
 * ```php
 * $parseFirst = firstValue(fn($str) => json_decode($str, true) ?: null);
 * $parseFirst($jsonStrings); // Returns first successfully parsed JSON
 *
 * $getUserName = firstValue(fn($user) => $user['name'] ?? null);
 * $getUserName($users); // Returns first user's name
 * ```
 *
 * @param  callable(mixed): mixed $c A function that receives each value from the iterable.
 *                                   Should return a truthy value to stop iteration and return
 *                                   that result, or a falsy value to continue searching.
 * @return Closure                a function that accepts an iterable and returns the first truthy result
 *                                from applying the callable, or null if no truthy results were produced
 */
function firstValue(callable $c): Closure
{
    return static function (iterable $it) use ($c): mixed {
        foreach ($it as $v) {
            if ($res = $c($v)) {
                return $res;
            }
        }

        return null;
    };
}
