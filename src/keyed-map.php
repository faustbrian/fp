<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function array_combine;
use function array_keys;
use function array_map;
use function array_values;

/**
 * Returns a higher-order function that transforms both keys and values of an array using separate mappers.
 *
 * This function creates a curried operation that applies independent transformation functions
 * to both the keys and values of an array, producing a new array with transformed key-value
 * pairs. Both mapper functions receive the original key and value as arguments, enabling
 * complex transformations based on the relationship between keys and values.
 *
 * If no key mapper is provided, keys are replaced with an auto-incrementing counter starting
 * from zero, effectively re-indexing the array with sequential numeric keys.
 *
 * ```php
 * // Transform both keys and values
 * $transform = keyedMap(
 *     values: fn($k, $v) => strtoupper($v),
 *     keys: fn($k, $v) => "prefix_$k"
 * );
 * $result = $transform(['name' => 'john', 'city' => 'paris']);
 * // ['prefix_name' => 'JOHN', 'prefix_city' => 'PARIS']
 *
 * // Re-index array (default key mapper)
 * $reindex = keyedMap(fn($k, $v) => $v * 2);
 * $result = $reindex(['a' => 5, 'b' => 10]); // [0 => 10, 1 => 20]
 *
 * // Swap keys and values
 * $swap = keyedMap(
 *     values: fn($k, $v) => $k,
 *     keys: fn($k, $v) => $v
 * );
 * ```
 *
 * @param  callable(int|string, mixed): mixed             $values The value transformation function that receives the
 *                                                                original key as the first argument and the value as
 *                                                                the second argument. Returns the transformed value.
 * @param  null|callable(int|string, mixed): (int|string) $keys   Optional key transformation function that
 *                                                                receives the original key as the first argument
 *                                                                and value as the second. Returns the new key.
 *                                                                Defaults to an auto-incrementing counter (0, 1, 2, ...)
 *                                                                if not provided.
 * @return Closure(array): array                          a curried function that accepts an array and returns a new array with
 *                                                        transformed keys and values based on the provided mapper functions
 */
function keyedMap(callable $values, ?callable $keys = null): Closure
{
    $keys ??= static function (): int {
        static $counter = 0;

        return $counter++;
    };

    return static fn (array $a): array => array_combine(
        array_map($keys, array_keys($a), array_values($a)),
        array_map($values, array_keys($a), array_values($a)),
    );
}
