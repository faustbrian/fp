<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function Cline\fp\first;

/**
 * Standard functional programming find operation.
 *
 * Alias for first() providing the conventional FP name. Returns the first
 * element from an iterable that satisfies the predicate function. If no
 * element matches, returns null.
 *
 * ```php
 * $findAdult = find(fn($user) => $user['age'] >= 18);
 * $findAdult($users); // First user with age >= 18
 * ```
 *
 * @param  callable(mixed): bool    $c Predicate function that receives each element value and returns bool to indicate a match
 * @return Closure(iterable): mixed Curried function accepting iterable and returning first matching element or null if no match found
 *
 * @see first() For the underlying implementation
 * @since 1.0.0
 */
function find(callable $c): Closure
{
    return first($c);
}
