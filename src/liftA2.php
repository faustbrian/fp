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
 * Lifts a binary function into an applicative context.
 *
 * Takes a curried binary function and applies it to two wrapped values,
 * producing all combinations. This is a specialized version of ap for
 * binary functions, fundamental to applicative-style programming.
 *
 * The function applies the binary function to every combination of values
 * from the two input arrays, creating a Cartesian product of results.
 *
 * ```php
 * $add = fn($a) => fn($b) => $a + $b;
 * $combine = liftA2($add);
 *
 * $combine([1, 2], [10, 20]); // [11, 21, 12, 22]
 *
 * $concat = fn($a) => fn($b) => $a . $b;
 * $liftedConcat = liftA2($concat);
 * $liftedConcat(['a', 'b'], ['1', '2']); // ['a1', 'a2', 'b1', 'b2']
 * ```
 *
 * @param  callable                     $fn Curried binary function (a -> b -> c)
 * @return Closure(array, array): array Function accepting two arrays and returning all combinations
 */
function liftA2(callable $fn): Closure
{
    return static function (array $as, array $bs) use ($fn): array {
        $result = [];

        foreach ($as as $a) {
            $partiallyApplied = $fn($a);

            foreach ($bs as $b) {
                /** @var callable $partiallyApplied */
                $result[] = $partiallyApplied($b);
            }
        }

        return $result;
    };
}
