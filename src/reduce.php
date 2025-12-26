<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function array_reduce;
use function is_array;

/**
 * Reduces an iterable to a single value using a reducer function.
 *
 * Returns a closure that accepts an iterable and reduces it to a single accumulated
 * value by iteratively applying the reducer function. For arrays, uses the optimized
 * array_reduce implementation. For other iterables, manually iterates and accumulates.
 *
 * The reducer function receives the accumulated value and current element, returning
 * the new accumulated value. This follows the standard reduce/fold pattern from
 * functional programming.
 *
 * ```php
 * $sum = pipe(
 *     [1, 2, 3, 4, 5],
 *     reduce(0, fn($acc, $n) => $acc + $n)
 * ); // 15
 *
 * $concat = pipe(
 *     ['hello', 'world'],
 *     reduce('', fn($acc, $str) => $acc . $str)
 * ); // 'helloworld'
 * ```
 *
 * @param  mixed    $init Initial accumulator value that is passed to the first reducer call
 *                        and serves as the default return value for empty iterables
 * @param  callable $c    Reducer function with signature (mixed $accumulator, mixed $value): mixed
 *                        that combines the accumulator with each element to produce a new accumulator
 * @return Closure  A closure with signature (iterable $it): mixed that reduces the iterable
 *                  to a single accumulated value using the provided reducer function
 */
function reduce(mixed $init, callable $c): Closure
{
    return static function (iterable $it) use ($init, $c): mixed {
        if (is_array($it)) {
            return array_reduce($it, $c, $init);
        }

        foreach ($it as $v) {
            $init = $c($init, $v);
        }

        return $init;
    };
}
