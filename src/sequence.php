<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_key_exists;
use function array_map;
use function count;
use function max;

/**
 * Transposes an array of arrays from [[a]] to [a].
 *
 * In functional programming, sequence inverts a structure of nested
 * applicative values. For arrays, it transposes a 2D array, swapping
 * rows and columns.
 *
 * This is useful for transforming an array of wrapped values into a
 * wrapped array of values, a fundamental operation in applicative
 * programming.
 *
 * ```php
 * sequence([[1, 2], [3, 4], [5, 6]]);
 * // [[1, 3, 5], [2, 4, 6]]
 *
 * sequence([[1, 2, 3]]);
 * // [[1], [2], [3]]
 *
 * sequence([]);
 * // []
 * ```
 *
 * @param  array<int, array<mixed>> $arrays Array of arrays to transpose
 * @return array                    Transposed array
 */
function sequence(array $arrays): array
{
    if ($arrays === []) {
        return [];
    }

    $result = [];
    $lengths = array_map(count(...), $arrays);
    $maxLength = max($lengths);

    for ($i = 0; $i < $maxLength; ++$i) {
        $column = [];

        foreach ($arrays as $array) {
            if (array_key_exists($i, $array) || array_key_exists($i, $array)) {
                $column[] = $array[$i];
            }
        }

        if ($column !== []) {
            $result[] = $column;
        }
    }

    return $result;
}
