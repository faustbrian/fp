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
 * Returns a higher-order function that transforms an iterable, passing both value and key to the mapper.
 *
 * This function creates a curried map operation that provides both the value and key to the
 * transformation function, enabling key-aware mapping logic. The returned closure accepts
 * an iterable and yields transformed values while preserving the original keys.
 *
 * This must be a separate function from itmap() because internal PHP functions no longer
 * accept extra arguments, while user-defined functions do. A combined function would be
 * incompatible with single-argument built-in functions like strtoupper() or trim().
 *
 * ```php
 * // Transform using both value and key
 * $addKeyToValue = itmapWithKeys(fn($v, $k) => "$k: $v");
 * $result = $addKeyToValue(['name' => 'John', 'age' => 30]);
 * // ['name' => 'name: John', 'age' => 'age: 30']
 *
 * // Use key for conditional transformation
 * $transform = itmapWithKeys(fn($v, $k) => str_starts_with($k, 'user_') ? strtoupper($v) : $v);
 * ```
 *
 * @see itmap() For mapping without providing keys to the transformation function.
 * @param  callable(mixed, mixed): mixed $c The transformation function that receives the value as the
 *                                          first argument and the key as the second argument. Returns
 *                                          the transformed value to yield in the result iterable.
 * @return Closure(iterable): iterable   A curried function that accepts an iterable and returns a mapped
 *                                       iterable yielding transformed values. Preserves keys from the
 *                                       original iterable.
 */
function itmapWithKeys(callable $c): Closure
{
    return static function (iterable $it) use ($c): iterable {
        foreach ($it as $k => $v) {
            yield $k => $c($v, $k);
        }
    };
}
