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
 * Creates a function that returns the first truthy result from applying a callable to iterable elements.
 *
 * Returns a curried function that iterates through an iterable, applying the provided
 * callable to each value and its key. Returns the first truthy result produced by the
 * callable, or null if all results are falsy. Unlike firstWithKeys (which returns the
 * matching element), this returns the result of the callable itself. Useful for finding
 * computed values, transformed results, or extracting nested data.
 *
 * ```php
 * $findUserId = firstValueWithKeys(fn($user, $key) => $user['active'] ? $user['id'] : null);
 * $findUserId($users); // Returns ID of first active user
 *
 * $extractValue = firstValueWithKeys(fn($item, $key) => $item[$key] ?? null);
 * $extractValue($data); // Returns first non-null extracted value
 * ```
 *
 * @param  callable(mixed, array-key): mixed $c A function that receives both the value and
 *                                              its key from the iterable. Should return a truthy
 *                                              value to stop iteration and return that value, or
 *                                              a falsy value to continue searching.
 * @return Closure                           a function that accepts an iterable and returns the first truthy result
 *                                           from applying the callable, or null if no truthy results were produced
 */
function firstValueWithKeys(callable $c): Closure
{
    return static function (iterable $it) use ($c): mixed {
        foreach ($it as $k => $v) {
            if ($res = $c($v, $k)) {
                return $res;
            }
        }

        return null;
    };
}
