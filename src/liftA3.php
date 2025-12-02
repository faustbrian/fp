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
 * Lifts a curried ternary function into an applicative context.
 *
 * Takes a curried ternary function and applies it to three wrapped values (arrays),
 * producing all combinations. This extends the applicative pattern to functions with
 * three arguments, enabling complex multi-parameter functional operations.
 *
 * The function applies the ternary function to every combination of values from the
 * three input arrays, creating a Cartesian product of results. The first array provides
 * values for the first parameter, the second for the second parameter, and the third
 * for the third parameter. All possible triplet combinations are evaluated.
 *
 * The input function must be curried, meaning it returns nested functions:
 * (a -> (b -> (c -> d))) rather than ((a, b, c) -> d).
 *
 * ```php
 * // Lift three-way addition
 * $sum3 = fn($a) => fn($b) => fn($c) => $a + $b + $c;
 * $combine = liftA3($sum3);
 * $combine([1, 2], [10, 20], [100]); // [111, 121, 112, 122, 211, 221, 212, 222]
 *
 * // Lift three-way concatenation
 * $concat3 = fn($a) => fn($b) => fn($c) => $a . $b . $c;
 * $liftedConcat = liftA3($concat3);
 * $liftedConcat(['a'], ['1'], ['x', 'y']); // ['a1x', 'a1y']
 *
 * // Build complex objects
 * $createRecord = fn($id) => fn($name) => fn($status) => [
 *     'id' => $id,
 *     'name' => $name,
 *     'status' => $status
 * ];
 * $generator = liftA3($createRecord);
 * $generator([1], ['Alice', 'Bob'], ['active', 'inactive']);
 * // [['id' => 1, 'name' => 'Alice', 'status' => 'active'],
 * //  ['id' => 1, 'name' => 'Alice', 'status' => 'inactive'],
 * //  ['id' => 1, 'name' => 'Bob', 'status' => 'active'],
 * //  ['id' => 1, 'name' => 'Bob', 'status' => 'inactive']]
 * ```
 *
 * @param  callable                            $fn A curried ternary function. The function receives the first
 *                                                 argument and returns a function that receives the second
 *                                                 argument, which returns a function that receives the third
 *                                                 argument.
 * @return Closure(array, array, array): array a function that accepts three arrays and returns
 *                                             an array containing results from applying the
 *                                             function to all combinations of values from all
 *                                             three input arrays
 *
 * @see lift() For lifting unary functions into applicative contexts.
 * @see liftA2() For lifting binary functions into applicative contexts.
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
