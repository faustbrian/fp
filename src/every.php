<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function Cline\fp\all;

/**
 * Tests whether all elements satisfy the predicate.
 *
 * Alias for all() using JavaScript's Array.every naming convention.
 * Returns true if every element in the iterable passes the predicate test.
 * Short-circuits on the first failure for optimal performance.
 *
 * ```php
 * $allPositive = every(fn($n) => $n > 0);
 * $allPositive([1, 2, 3, 4]); // true
 * $allPositive([1, -2, 3]); // false
 * ```
 *
 * @see all() For the underlying implementation
 * @param  callable $c Predicate function testing each element
 * @return Closure  Function accepting iterable and returning bool
 */
function every(callable $c): Closure
{
    return all($c);
}
