<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function Cline\fp\contains;

/**
 * Checks if an element exists in an iterable.
 *
 * Alias for contains() using the Haskell-style naming convention.
 * Returns a curried function that searches for a value in an iterable
 * using strict comparison.
 *
 * ```php
 * $hasThree = elem(3);
 * $hasThree([1, 2, 3, 4]); // true
 * $hasThree([1, 2, 4]); // false
 * ```
 *
 * @param  mixed                   $needle The element to search for
 * @return Closure(iterable): bool Function accepting iterable and returning bool
 *
 * @see contains() For the underlying implementation
 */
function elem(mixed $needle): Closure
{
    return contains($needle);
}
