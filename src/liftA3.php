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
 * Lifts a ternary function into an applicative context.
 *
 * Takes a curried ternary function and applies it to three wrapped values,
 * producing all combinations. This extends the applicative pattern to
 * functions with three arguments.
 *
 * The function applies the ternary function to every combination of values
 * from the three input arrays, creating a Cartesian product of results.
 *
 * ```php
 * $sum3 = fn($a) => fn($b) => fn($c) => $a + $b + $c;
 * $combine = liftA3($sum3);
 *
 * $combine([1, 2], [10, 20], [100]); // [111, 121, 112, 122, 211, 221, 212, 222]
 *
 * $concat3 = fn($a) => fn($b) => fn($c) => $a . $b . $c;
 * $liftedConcat = liftA3($concat3);
 * $liftedConcat(['a'], ['1'], ['x', 'y']); // ['a1x', 'a1y']
 * ```
 *
 * @param  callable                            $fn Curried ternary function (a -> b -> c -> d)
 * @return Closure(array, array, array): array Function accepting three arrays and returning all combinations
 */
function liftA3(callable $fn): Closure
{
    return static function (array $as, array $bs, array $cs) use ($fn): array {
        $result = [];

        foreach ($as as $a) {
            $partial1 = $fn($a);

            foreach ($bs as $b) {
                /** @var callable $partial1 */
                $partial2 = $partial1($b);

                foreach ($cs as $c) {
                    /** @var callable $partial2 */
                    $result[] = $partial2($c);
                }
            }
        }

        return $result;
    };
}
