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
 * Creates a closure that reduces an iterable with access to both keys and values.
 *
 * Performs reduction while providing both the key and value to the reducer
 * function, enabling key-aware aggregation operations. This is particularly
 * useful when the keys carry semantic meaning (associative arrays, named
 * properties) or when building indexed results.
 *
 * Implemented as a separate function from standard reduce() because PHP's
 * array_reduce() doesn't provide keys to callbacks. This custom implementation
 * allows optimization: standard reduce() can delegate to the faster built-in
 * array_reduce(), while this variant handles the key-aware case explicitly.
 *
 * ```php
 * // Build object from associative array
 * $toObject = reduceWithKeys(
 *     new stdClass(),
 *     fn($obj, $value, $key) => (function() use ($obj, $key, $value) {
 *         $obj->{$key} = $value;
 *         return $obj;
 *     })()
 * );
 *
 * // Sum values where key matches pattern
 * $sumMatching = reduceWithKeys(
 *     0,
 *     fn($sum, $val, $key) => str_starts_with($key, 'price_') ? $sum + $val : $sum
 * );
 * $total = $sumMatching(['price_1' => 100, 'tax' => 10, 'price_2' => 50]); // 150
 * ```
 *
 * @param  mixed    $init initial accumulator value that serves as the starting point for the
 *                        reduction and is returned if the iterable is empty
 * @param  callable $c    Reducer function with signature (mixed $accumulator, mixed $value, mixed $key): mixed.
 *                        Receives the current accumulator, element value, and element key for each iteration.
 * @return Closure  a function accepting an iterable that returns the final accumulated value after
 *                  processing all elements with their keys
 */
function reduceWithKeys(mixed $init, callable $c): Closure
{
    return static function (iterable $it) use ($init, $c): mixed {
        foreach ($it as $k => $v) {
            $init = $c($init, $v, $k);
        }

        return $init;
    };
}
