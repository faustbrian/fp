<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function array_map;
use function array_values;
use function min;

/**
 * Combine multiple arrays element-wise using a custom combiner function.
 *
 * Creates a higher-order function that merges multiple arrays by applying a
 * combiner function to elements at corresponding positions. The combiner receives
 * one element from each array as separate arguments and returns a single combined
 * value. The resulting array length matches the shortest input array, ensuring
 * all positions have values from every array.
 *
 * ```php
 * $add = fn($a, $b) => $a + $b;
 * $sumArrays = zipWith($add);
 * $result = $sumArrays([1, 2, 3], [10, 20, 30]);
 * // Result: [11, 22, 33]
 *
 * $createPair = fn($key, $value) => [$key => $value];
 * $pairUp = zipWith($createPair);
 * $result = $pairUp(['a', 'b'], [1, 2]);
 * // Result: [['a' => 1], ['b' => 2]]
 * ```
 *
 * @param  callable $combiner Function that combines elements from corresponding positions.
 *                            Receives one argument per input array and returns the combined result.
 * @return Closure  Returns a closure accepting variadic arrays and returning an array
 *                  of combined values. Length matches the shortest input array.
 */
function zipWith(callable $combiner): Closure
{
    return static function (array ...$arrays) use ($combiner): array {
        if ($arrays === []) {
            return [];
        }

        $result = [];
        $minLength = min(array_map('count', $arrays));

        for ($i = 0; $i < $minLength; ++$i) {
            $elements = [];

            foreach ($arrays as $array) {
                $elements[] = array_values($array)[$i];
            }

            $result[] = $combiner(...$elements);
        }

        return $result;
    };
}
