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
use function is_array;

/**
 * Maps an iterable using a transformation function that receives only values.
 *
 * Returns a curried function that transforms each element of the provided iterable
 * using the callback. Only values are passed to the callback, making it compatible
 * with PHP's internal single-argument functions. Keys are preserved in the resulting
 * array.
 *
 * ```php
 * // Double all numbers
 * $double = amap(fn($v) => $v * 2);
 * $result = $double([1, 2, 3]); // [2, 4, 6]
 *
 * // Compatible with internal functions
 * $uppercase = amap('strtoupper');
 * $result = $uppercase(['apple', 'banana']); // ['APPLE', 'BANANA']
 *
 * // Preserve associative array keys
 * $addPrefix = amap(fn($v) => "item_$v");
 * $result = $addPrefix(['a' => 1, 'b' => 2]); // ['a' => 'item_1', 'b' => 'item_2']
 * ```
 *
 * @param  callable(mixed): mixed   $c Transformation function receiving (mixed $value): mixed that
 *                                     transforms each element into a new value. Accepts internal PHP
 *                                     functions like 'strtoupper', 'trim', 'intval', etc.
 * @return Closure(iterable): array Function accepting an iterable and returning a transformed array
 *                                  with preserved keys. Each value is replaced by the result of
 *                                  applying the transformation function to the original value.
 *
 * @see amapWithKeys() For mapping with key access
 */
function amap(callable $c): Closure
{
    return static function (iterable $it) use ($c): array {
        if (is_array($it)) {
            return array_map($c, $it);
        }

        $result = [];

        foreach ($it as $k => $v) {
            $result[$k] = $c($v);
        }

        return $result;
    };
}
