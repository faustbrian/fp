<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function Cline\fp\reduce;

/**
 * Left fold operation - explicit left-to-right reduction.
 *
 * Alias for reduce() with explicit naming to indicate left-to-right
 * folding direction. Reduces an iterable from left to right, applying
 * the reducer function to each element with the accumulator.
 *
 * ```php
 * $sum = foldl(0, fn($acc, $n) => $acc + $n);
 * $sum([1, 2, 3, 4]); // 10
 *
 * $buildString = foldl('', fn($acc, $char) => $acc . $char);
 * $buildString(['a', 'b', 'c']); // 'abc'
 * ```
 *
 * @param  mixed    $init Initial accumulator value
 * @param  callable $c    Reducer function combining accumulator with each element
 * @return Closure  Function accepting iterable and returning accumulated value
 *
 * @see reduce() For the underlying implementation
 * @see foldr() For right-to-left reduction
 */
function foldl(mixed $init, callable $c): Closure
{
    return reduce($init, $c);
}
