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
 * Creates a closure that extracts a property value from an object.
 *
 * Returns a function that accesses the specified property from any object,
 * enabling property access in functional pipelines and compositions. This
 * is particularly useful for mapping operations, filtering by properties,
 * or chaining property access in point-free style.
 *
 * Only works with public properties due to PHP's visibility rules. Attempting
 * to access non-public properties will result in a runtime error.
 *
 * ```php
 * $getName = prop('name');
 * $user = (object)['name' => 'Alice', 'email' => 'alice@example.com'];
 * $name = $getName($user); // 'Alice'
 *
 * $users = [
 *     (object)['name' => 'Alice'],
 *     (object)['name' => 'Bob']
 * ];
 * $names = array_map(prop('name'), $users); // ['Alice', 'Bob']
 *
 * // Usage in pipeline
 * $email = pipe(
 *     $user,
 *     prop('profile'),
 *     prop('contact'),
 *     prop('email')
 * );
 * ```
 *
 * @param  string  $prop The name of the public property to extract from objects
 * @return Closure A function that accepts an object and returns the value of the specified
 *                 property. Accessing non-existent or non-public properties will cause errors.
 */
function prop(string $prop): Closure
{
    return static fn (object $o): mixed => $o->{$prop};
}
