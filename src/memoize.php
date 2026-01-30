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
use function serialize;

/**
 * Creates a memoized version of a function that caches results by argument signature.
 *
 * Wraps a function to store computed results in memory, avoiding redundant
 * calculations when called with identical arguments. The cache key is generated
 * by serializing all arguments, ensuring accurate cache hits even with complex
 * data structures. Particularly effective for expensive computations, recursive
 * algorithms, and frequently called functions with repeated inputs.
 *
 * Performance considerations: The cache persists for the lifetime of the memoized
 * function, so memory usage grows with unique argument combinations. Use judiciously
 * for functions with bounded input spaces or implement cache eviction separately.
 *
 * ```php
 * $fibonacci = function($n) use (&$fibonacci) {
 *     if ($n <= 1) return $n;
 *     return $fibonacci($n - 1) + $fibonacci($n - 2);
 * };
 * $fastFib = memoize($fibonacci);
 * $fastFib(35); // Fast even for large inputs
 *
 * // Expensive API call wrapper
 * $fetchUser = fn($id) => Http::get("/users/{$id}")->json();
 * $cachedFetch = memoize($fetchUser);
 * $cachedFetch(123); // Makes API call
 * $cachedFetch(123); // Returns cached result, no API call
 *
 * // Works with multiple arguments
 * $calculate = fn($a, $b, $c) => $a * $b + $c;
 * $memoizedCalc = memoize($calculate);
 * $memoizedCalc(5, 10, 3); // Computes: 53
 * $memoizedCalc(5, 10, 3); // Returns cached: 53
 * ```
 *
 * @param  callable $fn The function to memoize. Can accept any number and type
 *                      of arguments. All arguments must be serializable for
 *                      proper cache key generation.
 * @return Closure  Returns a memoized version that caches results in memory.
 *                  The returned function has the same signature as the original
 *                  but with automatic result caching based on input arguments.
 */
function memoize(callable $fn): Closure
{
    $cache = [];

    return static function (...$args) use ($fn, &$cache): mixed {
        $key = serialize($args);

        if (!array_key_exists($key, $cache)) {
            $cache[$key] = $fn(...$args);
        }

        return $cache[$key];
    };
}
