<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;
use stdClass;

use function array_key_exists;
use function is_array;
use function is_object;
use function property_exists;

/**
 * Creates a closure that extracts specified properties from arrays or objects.
 *
 * Returns a new data structure containing only the specified keys/properties,
 * preserving the original type (array input returns array, object input returns
 * stdClass object). Non-existent keys are silently ignored rather than included
 * with null values, ensuring the result contains only properties that actually
 * exist in the source data.
 *
 * This function is useful for creating DTOs, filtering API responses, or selecting
 * specific fields from database results while maintaining type consistency.
 *
 * ```php
 * $user = ['id' => 1, 'name' => 'John', 'email' => 'john@example.com', 'password' => 'secret'];
 * $pickPublic = pick('id', 'name', 'email');
 * $public = $pickPublic($user); // ['id' => 1, 'name' => 'John', 'email' => 'john@example.com']
 *
 * $obj = (object) ['a' => 1, 'b' => 2, 'c' => 3];
 * $pickAB = pick('a', 'b');
 * $result = $pickAB($obj); // stdClass {a: 1, b: 2}
 * ```
 *
 * @param  string  ...$keys Variable number of key/property names to extract from the data.
 *                          Keys that don't exist in the source data are silently ignored.
 * @return Closure A function accepting array|object data that returns array|object containing
 *                 only the specified keys. Returns empty array if input is neither array nor object.
 */
function pick(string ...$keys): Closure
{
    return static function (mixed $data) use ($keys): array|object {
        if (is_array($data)) {
            $result = [];

            foreach ($keys as $key) {
                if (array_key_exists($key, $data)) {
                    $result[$key] = $data[$key];
                }
            }

            return $result;
        }

        if (is_object($data)) {
            $result = new stdClass();

            foreach ($keys as $key) {
                if (property_exists($data, $key)) {
                    $result->{$key} = $data->{$key};
                }
            }

            return $result;
        }

        return [];
    };
}
