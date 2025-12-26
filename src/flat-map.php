<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function is_array;

/**
 * Maps a function over an iterable and flattens the result by one level.
 *
 * Combines map and flatten operations. The mapper function should return
 * an array for each element, and all resulting arrays are concatenated
 * into a single flat array. Keys are not preserved in the final result.
 * If the mapper returns a non-array value, it is added directly to the result.
 *
 * ```php
 * $duplicate = flatMap(fn($x) => [$x, $x]);
 * $duplicate([1, 2, 3]); // [1, 1, 2, 2, 3, 3]
 *
 * $explodeWords = flatMap(fn($s) => explode(' ', $s));
 * $explodeWords(['hello world', 'foo bar']); // ['hello', 'world', 'foo', 'bar']
 * ```
 *
 * @param  callable(mixed): mixed               $fn Transformation function that receives each element value and returns
 *                                                  an array to be flattened into the result, or a scalar value to be
 *                                                  added directly. Each returned array is concatenated into the final output.
 * @return Closure(iterable): array<int, mixed> Curried function accepting iterable and returning zero-indexed flattened array
 *
 * @since 1.0.0
 */
function flatMap(callable $fn): Closure
{
    return static function (iterable $it) use ($fn): array {
        $result = [];

        foreach ($it as $v) {
            $mapped = $fn($v);

            if (is_array($mapped)) {
                foreach ($mapped as $item) {
                    $result[] = $item;
                }
            } else {
                $result[] = $mapped;
            }
        }

        return $result;
    };
}
