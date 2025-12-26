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
 * Applicative apply - applies wrapped functions to wrapped values.
 *
 * The ap function is a core operation in applicative functors. It takes
 * an array of functions and returns a function that applies each function
 * to each value in an array, collecting all results.
 *
 * This enables applying multiple functions to multiple values in a
 * Cartesian product fashion, fundamental to applicative-style programming.
 *
 * ```php
 * $add = fn($a) => fn($b) => $a + $b;
 * $fns = [fn($x) => $x + 1, fn($x) => $x * 2];
 *
 * $applyFns = ap($fns);
 * $applyFns([10, 20]); // [11, 21, 20, 40]
 * ```
 *
 * @see map() For simpler one-to-one transformations
 * @see flatMap() For transformations that need flattening
 * @param  array<callable(mixed): mixed> $fns Array of functions to apply to each value. Each function
 *                                            is applied to every value in the input array, producing
 *                                            a flat array of all results in Cartesian product fashion.
 * @return Closure(array): array         Function accepting an array and returning an array of results
 *                                       from applying each function to each value. The result is flattened
 *                                       so that all function-value combinations are in a single array.
 */
function ap(array $fns): Closure
{
    return static function (array $values) use ($fns): array {
        $result = [];

        /** @var callable $fn */
        foreach ($fns as $fn) {
            foreach ($values as $value) {
                $result[] = $fn($value);
            }
        }

        return $result;
    };
}
