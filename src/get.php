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
 * Creates a safe property accessor with optional default value fallback.
 *
 * Returns a curried function that safely extracts a property or array key from data
 * structures. Works with both arrays and objects using null coalescing for safe access.
 * When the key doesn't exist, returns the specified default value instead of throwing errors.
 *
 * ```php
 * $users = [
 *     ['name' => 'Alice', 'email' => 'alice@example.com'],
 *     ['name' => 'Bob'],
 * ];
 *
 * $getName = get('name', 'Unknown');
 * $getEmail = get('email', 'no-email@example.com');
 *
 * array_map($getName, $users);  // ['Alice', 'Bob']
 * array_map($getEmail, $users); // ['alice@example.com', 'no-email@example.com']
 * ```
 *
 * @param  string                $key     The property or array key to access
 * @param  mixed                 $default The fallback value to return when the key doesn't exist
 * @return Closure(mixed): mixed Returns a function accepting array/object data that extracts
 *                               the specified key or returns the default value
 */
function get(string $key, mixed $default = null): Closure
{
    return static function (mixed $data) use ($key, $default): mixed {
        if (is_array($data)) {
            return $data[$key] ?? $default;
        }

        if (is_object($data)) {
            return $data->{$key} ?? $default;
        }

        return $default;
    };
}
