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
 * Returns the first element from an iterable that satisfies the predicate function.
 *
 * Creates a curried function that accepts an iterable and returns the first element
 * where the predicate returns true. If no element matches, returns null. This is
 * useful for finding specific items in collections without iterating through all elements.
 *
 * ```php
 * $users = [
 *     ['name' => 'Alice', 'age' => 25],
 *     ['name' => 'Bob', 'age' => 30],
 *     ['name' => 'Charlie', 'age' => 35],
 * ];
 *
 * $findAdult = first(fn($user) => $user['age'] >= 30);
 * $result = $findAdult($users); // ['name' => 'Bob', 'age' => 30]
 * ```
 *
 * @param  callable                 $c Predicate function that receives each value and returns bool to indicate a match
 * @return Closure(iterable): mixed Returns a function accepting an iterable that yields
 *                                  the first matching element or null if no match is found
 */
function first(callable $c): Closure
{
    return static function (iterable $it) use ($c): mixed {
        foreach ($it as $v) {
            if ($c($v)) {
                return $v;
            }
        }

        return null;
    };
}
