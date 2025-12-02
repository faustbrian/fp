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
 * Returns a higher-order function that filters an iterable, passing both value and key to the predicate.
 *
 * This function creates a curried filter that provides both the value and key to the
 * predicate function, enabling key-aware filtering logic. The returned closure accepts
 * an iterable and yields only the key-value pairs where the predicate returns true.
 * Keys are preserved in the output.
 *
 * This must be a separate function from itfilter() because internal PHP functions no
 * longer accept extra arguments, while user-defined functions do. A combined function
 * would be incompatible with single-argument built-in functions like is_numeric().
 *
 * ```php
 * // Filter by key pattern
 * $filterByKey = itfilterWithKeys(fn($v, $k) => str_starts_with($k, 'user_'));
 * $result = $filterByKey(['user_name' => 'John', 'admin_id' => 5]); // ['user_name' => 'John']
 *
 * // Filter by value and key relationship
 * $filterLongKeys = itfilterWithKeys(fn($v, $k) => strlen($k) > strlen($v));
 * ```
 *
 * @param  null|callable(mixed, mixed): bool $c Optional predicate function that receives the value
 *                                              as the first argument and the key as the second argument.
 *                                              Should return true to include the item, false to exclude it.
 *                                              Defaults to a truthiness check on the value if not provided.
 * @return Closure(iterable): iterable       A curried function that accepts an iterable and returns a filtered
 *                                           iterable yielding only items where the predicate returned true.
 *                                           Preserves keys from the original iterable.
 *
 * @see itfilter() For filtering without providing keys to the predicate function.
 */
function itfilterWithKeys(?callable $c = null): Closure
{
    $c ??= static fn (mixed $v, mixed $k): bool => (bool) $v;

    return static function (iterable $it) use ($c): iterable {
        foreach ($it as $k => $v) {
            if ($c($v, $k)) {
                yield $k => $v;
            }
        }
    };
}
