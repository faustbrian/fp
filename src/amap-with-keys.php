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
use function is_array;

/**
 * Maps an iterable using a transformation function that receives both values and keys.
 *
 * Returns a curried function that transforms each element of the provided iterable
 * using the callback. The callback receives both the value and key for each element,
 * allowing key-based transformation logic. Keys are preserved in the resulting array.
 *
 * This is a separate function from amap() because PHP's internal functions no longer
 * accept extra arguments, while user-defined functions do. A combined function would
 * be incompatible with single-argument internal array functions like array_map().
 *
 * ```php
 * // Transform values using both value and key
 * $prefixWithKey = amapWithKeys(fn($v, $k) => "$k: $v");
 * $result = $prefixWithKey(['a' => 'apple', 'b' => 'banana']); // ['a' => 'a: apple', 'b' => 'b: banana']
 *
 * // Calculate positions and values
 * $withPosition = amapWithKeys(fn($v, $k) => ['index' => $k, 'value' => $v * 2]);
 * $result = $withPosition([10, 20, 30]); // [0 => ['index' => 0, 'value' => 20], ...]
 * ```
 *
 * @param  callable(mixed, mixed): mixed $c Transformation function receiving (mixed $value, mixed $key): mixed
 *                                          that transforms each element into a new value. The function
 *                                          has access to both the value and its associated key for
 *                                          context-aware transformations.
 * @return Closure(iterable): array      Function accepting an iterable and returning a transformed array
 *                                       with preserved keys. Each value is replaced by the result of
 *                                       applying the transformation function to the original value and key.
 *
 * @see amap() For mapping without key access
 */
function amapWithKeys(callable $c): Closure
{
    return static function (iterable $it) use ($c): array {
        if (is_array($it)) {
            // Ensure that the keys are preserved in the result.
            $keys = array_keys($it);

            return array_combine($keys, array_map($c, $it, $keys));
        }

        $result = [];

        foreach ($it as $k => $v) {
            $result[$k] = $c($v, $k);
        }

        return $result;
    };
}
