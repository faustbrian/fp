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
use function is_object;

/**
 * Creates a closure that extracts a single property from each element in a collection.
 *
 * Returns an array containing the specified property/key value from each element,
 * preserving the original keys. Works with both arrays and objects, using array
 * access for arrays and property access for objects. Missing properties return
 * null rather than throwing errors, ensuring safe extraction even from inconsistent
 * data structures.
 *
 * This is particularly useful for extracting columns from database results, mapping
 * object collections to value arrays, or preparing data for select dropdowns.
 *
 * ```php
 * $users = [
 *     ['id' => 1, 'name' => 'Alice'],
 *     ['id' => 2, 'name' => 'Bob'],
 *     ['id' => 3] // Missing 'name' key
 * ];
 * $names = pluck('name')($users); // [0 => 'Alice', 1 => 'Bob', 2 => null]
 *
 * $objects = [
 *     (object)['id' => 1, 'email' => 'alice@example.com'],
 *     (object)['id' => 2, 'email' => 'bob@example.com']
 * ];
 * $emails = pluck('email')($objects); // [0 => 'alice@example.com', 1 => 'bob@example.com']
 * ```
 *
 * @param  int|string $key The property name (for objects) or array key (for arrays) to extract
 *                         from each element. Supports both string keys and numeric indices.
 * @return Closure    a function accepting an iterable collection that returns an array containing
 *                    the specified property value from each element, with null for missing properties
 *
 * @api
 */
function pluck(string|int $key): Closure
{
    return static function (iterable $it) use ($key): array {
        $result = [];

        foreach ($it as $k => $v) {
            if (is_array($v)) {
                $result[$k] = $v[$key] ?? null;
            } elseif (is_object($v)) {
                $result[$k] = $v->{$key} ?? null;
            }
        }

        return $result;
    };
}
