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
 * @param  callable $c Predicate function that returns bool for each element
 * @return Closure  Function accepting iterable and returning first match or null
 *
 * @see first() For the underlying implementation
 */
function find(callable $c): Closure
{
    return first($c);
}
