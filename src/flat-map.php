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
 *
 * ```php
 * $duplicate = flatMap(fn($x) => [$x, $x]);
 * $duplicate([1, 2, 3]); // [1, 1, 2, 2, 3, 3]
 *
 * $explodeWords = flatMap(fn($s) => explode(' ', $s));
 * $explodeWords(['hello world', 'foo bar']); // ['hello', 'world', 'foo', 'bar']
 * ```
 *
 * @param  callable                 $fn Mapper function that returns an array for each element
 * @return Closure(iterable): array Function accepting iterable and returning flattened mapped array
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
