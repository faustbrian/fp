<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function array_key_exists;
use function is_object;
use function serialize;
use function spl_object_hash;

/**
 * Remove duplicate values from an iterable while preserving keys.
 *
 * Creates a higher-order function that filters an iterable to contain only
 * unique values. Uses object hashing for object comparison and serialization
 * for scalar values to determine uniqueness. When duplicates are encountered,
 * the first occurrence is kept and subsequent duplicates are discarded.
 *
 * ```php
 * $numbers = [1, 2, 2, 3, 3, 3, 4];
 * $uniqueNumbers = unique()($numbers);
 * // Result: [0 => 1, 1 => 2, 3 => 3, 6 => 4]
 *
 * $objects = [new User('alice'), new User('bob'), new User('alice')];
 * $uniqueObjects = unique()($objects);
 * // Result: [0 => User('alice'), 1 => User('bob')]
 * ```
 *
 * @return Closure(iterable): array returns a closure that accepts an iterable
 *                                  and returns an array containing only unique
 *                                  values with their original keys preserved
 */
function unique(): Closure
{
    return static function (iterable $it): array {
        $result = [];
        $seen = [];

        foreach ($it as $k => $v) {
            $key = is_object($v) ? spl_object_hash($v) : serialize($v);

            if (!array_key_exists($key, $seen)) {
                $seen[$key] = true;
                $result[$k] = $v;
            }
        }

        return $result;
    };
}
