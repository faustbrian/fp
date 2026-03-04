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
 * Sets a property or key immutably, returning a modified copy.
 *
 * Returns a closure that sets a property on arrays or objects without mutating the original.
 * For arrays, creates a shallow copy with the key updated. For objects, clones the object and
 * sets the property. For other types, returns a new array containing the key-value pair.
 *
 * This follows immutable data patterns where modifications produce new instances rather than
 * mutating existing data structures. Objects are cloned to preserve the original instance.
 *
 * ```php
 * $updated = pipe(
 *     ['name' => 'John', 'age' => 30],
 *     set('age', 31)
 * ); // ['name' => 'John', 'age' => 31]
 *
 * $modified = pipe(
 *     new stdClass(),
 *     set('status', 'active')
 * ); // stdClass with status property
 * ```
 *
 * @param  string  $key   Property name for objects or array key to set. For objects, this may create
 *                        a dynamic property, which triggers deprecation notices in PHP 8.2+ unless
 *                        the class explicitly allows dynamic properties.
 * @param  mixed   $value Value to assign to the specified key or property. Can be any type including
 *                        null, and will replace any existing value at that key.
 * @return Closure A closure with signature (mixed $data): array|object that returns a modified
 *                 copy with the property set. Arrays return arrays, objects return cloned objects,
 *                 and other types return a new single-element array.
 */
function set(string $key, mixed $value): Closure
{
    return static function (mixed $data) use ($key, $value): array|object {
        if (is_array($data)) {
            $result = $data;
            $result[$key] = $value;

            return $result;
        }

        if (is_object($data)) {
            $result = clone $data;
            // Suppress deprecation warning for dynamic property creation in PHP 8.2+
            $result->{$key} = $value;

            return $result;
        }

        return [$key => $value];
    };
}
