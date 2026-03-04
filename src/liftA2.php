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
 * Lifts a curried binary function into an applicative context.
 *
 * Takes a curried binary function and applies it to two wrapped values (arrays),
 * producing all combinations. This is a specialized version of applicative functor's
 * ap operation for binary functions, fundamental to applicative-style programming.
 *
 * The function applies the binary function to every combination of values from the
 * two input arrays, creating a Cartesian product of results. The first array provides
 * values for the first parameter, and the second array provides values for the second
 * parameter. All possible pairings are evaluated.
 *
 * The input function must be curried, meaning it returns a function that takes the
 * second argument: (a -> (b -> c)) rather than ((a, b) -> c).
 *
 * ```php
 * // Lift addition
 * $add = fn($a) => fn($b) => $a + $b;
 * $combine = liftA2($add);
 * $combine([1, 2], [10, 20]); // [11, 21, 12, 22]
 *
 * // Lift string concatenation
 * $concat = fn($a) => fn($b) => $a . $b;
 * $liftedConcat = liftA2($concat);
 * $liftedConcat(['a', 'b'], ['1', '2']); // ['a1', 'a2', 'b1', 'b2']
 *
 * // Combine object properties
 * $merge = fn($name) => fn($age) => ['name' => $name, 'age' => $age];
 * $createUsers = liftA2($merge);
 * $createUsers(['Alice', 'Bob'], [25, 30]);
 * // [['name' => 'Alice', 'age' => 25], ['name' => 'Alice', 'age' => 30],
 * //  ['name' => 'Bob', 'age' => 25], ['name' => 'Bob', 'age' => 30]]
 * ```
 *
 * @see lift() For lifting unary functions into applicative contexts.
 * @see liftA3() For lifting ternary functions into applicative contexts.
 * @param  callable                     $fn A curried binary function. The function receives the first
 *                                          argument and returns another function that receives the
 *                                          second argument.
 * @return Closure(array, array): array a function that accepts two arrays and returns an array
 *                                      containing results from applying the function to all
 *                                      combinations of values from both input arrays
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
