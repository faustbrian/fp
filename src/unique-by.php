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

/**
 * Creates a function that removes duplicate values based on a key extraction function.
 *
 * Returns a curried function that filters an iterable to contain only unique
 * elements, where uniqueness is determined by the result of a key function.
 * The key function transforms each value into a comparable key, and only the
 * first occurrence of each unique key is retained. Original array keys are preserved.
 *
 * This is useful when you need to deduplicate objects or complex values based
 * on a specific property or computed value, rather than the entire value.
 *
 * ```php
 * $uniqueByLength = uniqueBy(fn($s) => strlen($s));
 * $uniqueByLength(['a', 'bb', 'c', 'dd']); // Returns: ['a', 'bb'] (first of each length)
 *
 * // Deduplicate users by email
 * $uniqueByEmail = uniqueBy(fn($user) => $user->email);
 * $users = $uniqueByEmail($allUsers);
 *
 * // With associative arrays
 * $products = [
 *     'p1' => ['id' => 1, 'name' => 'Widget'],
 *     'p2' => ['id' => 2, 'name' => 'Gadget'],
 *     'p3' => ['id' => 1, 'name' => 'Widget Deluxe'], // Duplicate ID
 * ];
 * $uniqueById = uniqueBy(fn($p) => $p['id']);
 * $uniqueById($products); // Returns: ['p1' => [...], 'p2' => [...]] (keys preserved)
 * ```
 *
 * @param  callable(mixed): mixed $keyFn A function that receives each value and returns a key
 *                                       for uniqueness comparison. The key can be any type that
 *                                       works as an array key (string, int). Keys are compared
 *                                       using strict equality.
 * @return Closure                A function that accepts an iterable and returns an array containing only
 *                                the first occurrence of each unique key. Original array keys are preserved
 *                                in the result, making this suitable for associative arrays.
 */
function uniqueBy(callable $keyFn): Closure
{
    return static function (iterable $it) use ($keyFn): array {
        $result = [];
        $seen = [];

        foreach ($it as $k => $v) {
            $key = $keyFn($v);

            if (!array_key_exists($key, $seen)) {
                $seen[$key] = true;
                $result[$k] = $v;
            }
        }

        return $result;
    };
}
