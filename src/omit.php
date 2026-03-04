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

use function array_flip;
use function array_key_exists;
use function get_object_vars;
use function is_array;
use function is_object;

/**
 * Creates a function that excludes specified properties from objects or arrays.
 *
 * Returns a curried function that creates a shallow copy of the input data with
 * the specified keys removed. Works with both arrays and objects, preserving the
 * original data structure type. Non-array and non-object inputs return an empty
 * array as a safe fallback.
 *
 * ```php
 * $removeSecrets = omit('password', 'apiKey');
 *
 * $user = ['id' => 1, 'name' => 'John', 'password' => 'secret'];
 * $removeSecrets($user); // ['id' => 1, 'name' => 'John']
 *
 * $obj = (object)['id' => 1, 'name' => 'John', 'password' => 'secret'];
 * $removeSecrets($obj); // stdClass with id and name only
 *
 * // In pipelines
 * $users = pipe(
 *     $users,
 *     map(omit('password', 'ssn'))
 * );
 * ```
 *
 * @param  string  ...$keys Variable number of property/key names to exclude from the
 *                          resulting data structure. Keys are compared as strings and
 *                          must match exactly to be omitted.
 * @return Closure A curried function that accepts array|object data and returns
 *                 a new array|object with the specified keys removed, preserving
 *                 the original type. Returns empty array for invalid input types.
 */
function omit(string ...$keys): Closure
{
    return static function (mixed $data) use ($keys): array|object {
        $keysToOmit = array_flip($keys);

        if (is_array($data)) {
            $result = [];

            foreach ($data as $key => $value) {
                if (array_key_exists($key, $keysToOmit)) {
                    continue;
                }

                $result[$key] = $value;
            }

            return $result;
        }

        if (is_object($data)) {
            $result = new stdClass();

            foreach (get_object_vars($data) as $key => $value) {
                if (array_key_exists($key, $keysToOmit)) {
                    continue;
                }

                $result->{$key} = $value;
            }

            return $result;
        }

        return [];
    };
}
