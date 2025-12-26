<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function class_exists;
use function interface_exists;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_resource;
use function is_string;

/**
 * Creates a type-checking predicate function for filtering or validation.
 *
 * Returns a callable that checks if a value matches the specified type.
 * Supports both scalar types (int, string, float, bool, array, resource)
 * and object types (classes/interfaces via instanceof). This is particularly
 * useful for filtering collections or validating data in functional pipelines.
 *
 * For scalar types, uses the corresponding is_*() function. For class or
 * interface names, performs instanceof checks with automatic class/interface
 * existence validation.
 *
 * ```php
 * $isInt = typeIs('int');
 * $isInt(42); // Returns: true
 * $isInt('42'); // Returns: false
 *
 * $isString = typeIs('string');
 * array_filter([1, 'hello', 2, 'world'], typeIs('string')); // Returns: ['hello', 'world']
 *
 * // With objects
 * $isUser = typeIs(User::class);
 * $users = array_filter($mixed, $isUser);
 *
 * // In a pipeline
 * $integers = pipe(
 *     [1, 'a', 2, 'b', 3],
 *     filter(typeIs('int'))
 * ); // Returns: [1, 2, 3]
 * ```
 *
 * @param  string  $type The type to check against. For scalar types, use 'int', 'string',
 *                       'float', 'bool', 'array', or 'resource'. For objects, provide the
 *                       fully qualified class or interface name. The type string is case-sensitive
 *                       and must match exactly (e.g., 'int' not 'integer').
 * @return Closure A predicate function that accepts a value and returns true if the value
 *                 matches the specified type, false otherwise. The function uses strict
 *                 type checking with no type coercion.
 */
function typeIs(string $type): Closure
{
    return static fn (mixed $v): bool => match (true) {
        $type === 'int' => is_int($v),
        $type === 'string' => is_string($v),
        $type === 'float' => is_float($v),
        $type === 'bool' => is_bool($v),
        $type === 'array' => is_array($v),
        $type === 'resource' => is_resource($v),
        class_exists($type), interface_exists($type) => $v instanceof $type,
        default => false,
    };
}
