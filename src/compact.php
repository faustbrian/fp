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
 * Removes null and false values from an iterable, preserving keys.
 *
 * Returns a closure that filters out null and false values from any iterable
 * while retaining all other values, including empty strings, zeros, and empty
 * arrays. This differs from array_filter() which removes all falsy values.
 * Original keys are preserved in the result. This is a curried function for
 * use in functional pipelines. Particularly useful for cleaning data while
 * preserving meaningful falsy values like 0 or empty strings.
 *
 * ```php
 * $compactArray = compact();
 * $compactArray([1, null, 2, false, 0, '']); // [1, 2, 0, '']
 * $compactArray(['a' => 1, 'b' => null, 'c' => false, 'd' => 0]);
 * // ['a' => 1, 'd' => 0]
 *
 * // Clean API response data
 * $cleanResponse = compose(
 *     compact(),
 *     json_encode
 * );
 * $cleanResponse(['name' => 'John', 'age' => null, 'active' => false]);
 * // {"name":"John"} - removes null/false but keeps other values
 * ```
 *
 * @return Closure Returns a closure accepting an iterable and returning an array
 *                 with null and false values removed, other values and their keys
 *                 preserved. Unlike PHP's array_filter(), keeps falsy values like
 *                 0, '', and []. Only strictly null and strictly false are removed.
 */
function compact(): Closure
{
    return static function (iterable $it): array {
        $result = [];

        foreach ($it as $k => $v) {
            if ($v === null) {
                continue;
            }

            if ($v === false) {
                continue;
            }

            $result[$k] = $v;
        }

        return $result;
    };
}
