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
 * Like reduce but returns all intermediate accumulated values.
 *
 * Returns a curried function that reduces an iterable while collecting
 * each intermediate accumulation step. The result is an array containing
 * the initial value followed by the result after each element is processed.
 * Useful for tracking the progression of an accumulation.
 *
 * ```php
 * $runningSum = scan(0, fn($acc, $n) => $acc + $n);
 * $runningSum([1, 2, 3, 4]); // [0, 1, 3, 6, 10]
 *
 * $runningProduct = scan(1, fn($acc, $n) => $acc * $n);
 * $runningProduct([2, 3, 4]); // [1, 2, 6, 24]
 * ```
 *
 * @param  mixed    $init Initial accumulator value
 * @param  callable $fn   Reducer function with signature (mixed $acc, mixed $value): mixed
 * @return Closure  Function accepting iterable and returning array of intermediate values
 */
function scan(mixed $init, callable $fn): Closure
{
    return static function (iterable $it) use ($init, $fn): array {
        $results = [$init];
        $acc = $init;

        foreach ($it as $v) {
            $acc = $fn($acc, $v);
            $results[] = $acc;
        }

        return $results;
    };
}
