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
 * @param  array<callable>       $fns Array of functions to apply
 * @return Closure(array): array Function accepting array and returning array of results
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
