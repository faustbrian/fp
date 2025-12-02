<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function is_array;
use function iterator_to_array;

/**
 * Converts any iterable into an array, preserving keys.
 *
 * Materializes lazy iterables (generators, iterators) into concrete arrays.
 * For arrays, returns the input unchanged for efficiency. This is commonly
 * used at the end of functional pipelines to convert generator results into
 * arrays for further processing or return values. The function preserves all
 * keys, making it safe for associative arrays and iterators with string keys.
 *
 * ```php
 * $generator = (function() {
 *     yield 'a' => 1;
 *     yield 'b' => 2;
 * })();
 * collect($generator); // ['a' => 1, 'b' => 2]
 *
 * collect([1, 2, 3]); // [1, 2, 3] (unchanged, no iteration overhead)
 *
 * // Materialize pipeline results
 * $result = compose(
 *     map(fn($x) => $x * 2),
 *     filter(fn($x) => $x > 10),
 *     collect
 * );
 * $result([3, 6, 9]); // [12, 18] as array
 * ```
 *
 * @param  iterable<mixed, mixed> $a Any iterable to convert to an array.
 *                                   Arrays are returned as-is for O(1) performance,
 *                                   while iterators and generators are fully iterated
 *                                   and their key-value pairs collected into an array.
 * @return array<mixed, mixed>    Array containing all elements from the iterable
 *                                with their original keys preserved. For generators
 *                                with duplicate keys, later values overwrite earlier ones.
 */
function collect(iterable $a): array
{
    return is_array($a) ? $a : iterator_to_array($a);
}
