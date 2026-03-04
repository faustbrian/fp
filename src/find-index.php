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
 * @param  callable(mixed): bool                $predicate Function that receives each element value and returns bool to indicate a match
 * @return Closure(iterable): (null|int|string) Curried function accepting iterable and returning the matching key or null if no match found
 *
 * @since 1.0.0
 */
function findIndex(callable $predicate): Closure
{
    return static function (iterable $it) use ($predicate): int|string|null {
        foreach ($it as $k => $v) {
            if ($predicate($v)) {
                /** @var int|string $k */
                return $k;
            }
        }

        return null;
    };
}
