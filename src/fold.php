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
 * Standard functional programming fold operation.
 *
 * Alias for reduce() providing the conventional FP name. Reduces an iterable
 * to a single value by iteratively applying a reducer function from left to
 * right. Common name in functional programming literature.
 *
 * ```php
 * $sum = fold(0, fn($acc, $n) => $acc + $n);
 * $sum([1, 2, 3, 4]); // 10
 * ```
 *
 * @see reduce() For the underlying implementation
 * @param  mixed    $init Initial accumulator value
 * @param  callable $c    Reducer function combining accumulator with each element
 * @return Closure  Function accepting iterable and returning accumulated value
 */
function fold(mixed $init, callable $c): Closure
{
    return reduce($init, $c);
}
