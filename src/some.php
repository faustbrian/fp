<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function Cline\fp\any;

/**
 * Tests whether at least one element satisfies the predicate.
 *
 * Alias for any() using JavaScript's Array.some naming convention.
 * Returns true if any element in the iterable passes the predicate test.
 * Short-circuits on the first match for optimal performance.
 *
 * ```php
 * $hasEven = some(fn($n) => $n % 2 === 0);
 * $hasEven([1, 3, 4, 5]); // true
 * $hasEven([1, 3, 5]); // false
 * ```
 *
 * @see any() For the underlying implementation
 * @param  callable $c Predicate function testing each element
 * @return Closure  Function accepting iterable and returning bool
 */
function some(callable $c): Closure
{
    return any($c);
}
