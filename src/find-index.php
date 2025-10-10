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
 * Finds the index/key of the first element matching a predicate.
 *
 * Returns a curried function that searches through an iterable and returns
 * the key of the first element where the predicate returns true. Returns
 * null if no element matches. Keys are preserved (string or numeric).
 *
 * ```php
 * $findEven = findIndex(fn($x) => $x % 2 === 0);
 * $findEven([1, 3, 4, 5]); // 2
 *
 * $findAdult = findIndex(fn($u) => $u['age'] >= 18);
 * $findAdult($users); // Key of first adult user
 * ```
 *
 * @param  callable                 $predicate Function that returns bool for each element
 * @return Closure(iterable): mixed Function accepting iterable and returning key or null
 */
function findIndex(callable $predicate): Closure
{
    return static function (iterable $it) use ($predicate): mixed {
        foreach ($it as $k => $v) {
            if ($predicate($v)) {
                return $k;
            }
        }

        return null;
    };
}
