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
 * Appends a value to an array, optionally at a specific key.
 *
 * Returns a closure that adds the specified value to an array. When a key is
 * provided, the value is assigned to that key (overwriting any existing value).
 * Without a key, the value is appended using the next numeric index. This is
 * a curried function for use in functional pipelines, enabling partial application
 * for repeated append operations.
 *
 * ```php
 * $addItem = append('new item');
 * $addItem(['a', 'b']); // ['a', 'b', 'new item']
 *
 * $setKey = append('value', 'key');
 * $setKey(['a' => 1]); // ['a' => 1, 'key' => 'value']
 *
 * // Use in pipelines
 * $addMetadata = compose(
 *     append(time(), 'timestamp'),
 *     append('processed', 'status')
 * );
 * $addMetadata(['id' => 123]); // ['id' => 123, 'timestamp' => ..., 'status' => 'processed']
 * ```
 *
 * @param  mixed   $value The value to append to the array. Can be any type including
 *                        arrays, objects, scalars, or null.
 * @param  mixed   $key   Optional key at which to set the value. When null, the value
 *                        is appended with the next numeric index. When provided, sets
 *                        the value at the specified key, overwriting if it exists.
 * @return Closure Returns a closure accepting an array and returning the modified
 *                 array with the value appended or set at the specified key. The
 *                 original array is not mutated; a modified copy is returned.
 */
function append(mixed $value, mixed $key = null): Closure
{
    return static function (array $it) use ($value, $key): array {
        if ($key) {
            $it[$key] = $value;
        } else {
            $it[] = $value;
        }

        return $it;
    };
}
