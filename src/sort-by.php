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
use function iterator_to_array;
use function uasort;

/**
 * Sorts an iterable by values extracted from each element via a key function.
 *
 * Returns a closure that sorts elements based on the comparable values returned by the key
 * function for each element. The key function extracts the sort criterion from each element,
 * and elements are ordered using spaceship operator comparison of these extracted values.
 *
 * Preserves array keys during sorting using uasort. This is useful for sorting by computed
 * values, object properties, or nested array values without modifying the original data structure.
 *
 * ```php
 * $sorted = pipe(
 *     [['name' => 'John', 'age' => 30], ['name' => 'Jane', 'age' => 25]],
 *     sortBy(fn($user) => $user['age'])
 * ); // [1 => ['name' => 'Jane', 'age' => 25], 0 => ['name' => 'John', 'age' => 30]]
 *
 * $byLength = pipe(
 *     ['hello', 'hi', 'hey'],
 *     sortBy(fn($str) => strlen($str))
 * ); // [1 => 'hi', 2 => 'hey', 0 => 'hello']
 * ```
 *
 * @param  callable $keyFn Key extraction function with signature (mixed $element): mixed that
 *                         receives each element and returns a comparable value to sort by.
 *                         The returned values are compared using the spaceship operator (<=>).
 * @return Closure  A closure with signature (iterable $it): array that sorts the iterable
 *                  by the extracted key values and returns the sorted array with preserved keys
 *
 * @see sortWith() For sorting with a custom comparator function
 */
function sortBy(callable $keyFn): Closure
{
    return static function (iterable $it) use ($keyFn): array {
        $result = is_array($it) ? $it : iterator_to_array($it);
        uasort($result, static fn ($a, $b): int => $keyFn($a) <=> $keyFn($b));

        return $result;
    };
}
