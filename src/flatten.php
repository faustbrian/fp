<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_walk_recursive;

/**
 * Flattens a multi-dimensional array into a single-level array.
 *
 * Recursively walks through all nested arrays and extracts values into a flat
 * structure. Original keys are discarded and the result is a zero-indexed array
 * containing all leaf values in depth-first order. This function processes arrays
 * of any nesting depth and extracts all non-array values.
 *
 * ```php
 * $nested = [
 *     'a' => 1,
 *     'b' => [2, 3, ['c' => 4]],
 *     'd' => [5, [6, 7]]
 * ];
 *
 * $flat = flatten($nested); // [1, 2, 3, 4, 5, 6, 7]
 * ```
 *
 * @param  array<mixed>      $arr Multi-dimensional array with arbitrary nesting depth to be flattened.
 *                                All nested arrays are recursively traversed and their leaf values
 *                                are extracted into a single-level structure.
 * @return array<int, mixed> Zero-indexed array containing all leaf values from the input in depth-first traversal order
 *
 * @since 1.0.0
 */
function flatten(array $arr): array
{
    $flat = [];
    array_walk_recursive($arr, static function ($v) use (&$flat): void {
        $flat[] = $v;
    });

    return $flat;
}
