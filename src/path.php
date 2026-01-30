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
use function explode;
use function is_array;
use function is_object;
use function property_exists;

/**
 * Retrieves a value from a nested data structure using dot notation path.
 *
 * Creates a closure that safely traverses nested arrays and objects using dot-separated
 * path segments. Returns null if any segment in the path does not exist, preventing
 * errors when accessing deeply nested properties that may be undefined.
 *
 * Supports both array access (via array_key_exists) and object property access
 * (via property_exists), making it versatile for working with mixed data structures.
 * Each segment of the path is evaluated sequentially, allowing safe navigation through
 * complex nested data without manual null checks.
 *
 * ```php
 * $data = ['user' => ['address' => ['city' => 'New York']]];
 * $getCity = path('user.address.city');
 * $city = $getCity($data); // 'New York'
 *
 * $getMissing = path('user.profile.age');
 * $age = $getMissing($data); // null (safe, no error)
 * ```
 *
 * @param  string  $path Dot-separated path to the target property (e.g., 'user.address.city').
 *                       Each segment represents a key in an array or property name in an object.
 * @return Closure a function that accepts mixed data (array|object) and returns the value
 *                 at the specified path, or null if any path segment does not exist
 *
 * @api
 */
function path(string $path): Closure
{
    return static function (mixed $data) use ($path): mixed {
        $keys = explode('.', $path);
        $current = $data;

        foreach ($keys as $key) {
            if (is_array($current) && array_key_exists($key, $current)) {
                $current = $current[$key];
            } elseif (is_object($current) && property_exists($current, $key)) {
                $current = $current->{$key};
            } else {
                return null;
            }
        }

        return $current;
    };
}
