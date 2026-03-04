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
 * Returns a higher-order function that transforms each value in an iterable using a mapper function.
 *
 * This function creates a curried map operation that applies a transformation function to
 * each value in an iterable, yielding the transformed results while preserving the original
 * keys. The operation is lazy, processing values only as they are consumed.
 *
 * This function only passes values to the mapper, making it compatible with built-in PHP
 * functions like strtoupper(), trim(), intval(), etc. For key-aware mapping, use itmapWithKeys().
 *
 * ```php
 * // Transform numbers
 * $double = itmap(fn($n) => $n * 2);
 * $result = $double([1, 2, 3]); // [0 => 2, 1 => 4, 2 => 6]
 *
 * // Use with built-in functions
 * $uppercase = itmap('strtoupper');
 * $result = $uppercase(['hello', 'world']); // [0 => 'HELLO', 1 => 'WORLD']
 *
 * // Chain with pipe operations
 * $transform = pipe(
 *     itmap(fn($n) => $n * 2),
 *     itfilter(fn($n) => $n > 5)
 * );
 * ```
 *
 * @see itmapWithKeys() For mapping with access to both keys and values in the transformation.
 * @param  callable(mixed): mixed      $c The transformation function that receives each value and returns
 *                                        the transformed value. Compatible with built-in PHP functions
 *                                        like strtoupper(), trim(), intval(), etc.
 * @return Closure(iterable): iterable A curried function that accepts an iterable and returns a mapped
 *                                     iterable yielding transformed values. Preserves keys from the
 *                                     original iterable.
 */
function itmap(callable $c): Closure
{
    return static function (iterable $it) use ($c): iterable {
        foreach ($it as $k => $v) {
            yield $k => $c($v);
        }
    };
}
