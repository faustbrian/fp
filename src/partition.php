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
 * Creates a function that splits an iterable into two arrays based on a predicate.
 *
 * Returns a curried function that partitions iterable data into a tuple of two arrays:
 * the first contains all elements where the predicate returns true, and the second
 * contains elements where it returns false. Preserves original keys from the iterable,
 * making it useful for filtering while maintaining both matched and unmatched items.
 *
 * ```php
 * $isEven = fn($n) => $n % 2 === 0;
 * $partitionEven = partition($isEven);
 *
 * [$evens, $odds] = $partitionEven([1, 2, 3, 4, 5, 6]);
 * // $evens: [1 => 2, 3 => 4, 5 => 6]
 * // $odds: [0 => 1, 2 => 3, 4 => 5]
 *
 * // Separating valid and invalid data
 * $isValid = fn($user) => isset($user['email']);
 * [$valid, $invalid] = partition($isValid)($users);
 *
 * // In pipelines
 * [$active, $inactive] = pipe(
 *     $users,
 *     partition(fn($u) => $u['status'] === 'active')
 * );
 * ```
 *
 * @param  callable $predicate The predicate function to test each element. Receives each
 *                             value from the iterable and should return a boolean indicating
 *                             whether the element belongs in the first (true) or second (false)
 *                             partition array.
 * @return Closure  A curried function that accepts an iterable and returns a two-element
 *                  array tuple where index 0 contains values that passed the predicate
 *                  and index 1 contains values that failed the predicate
 *
 * @phpstan-return Closure(iterable): array{0: array<mixed>, 1: array<mixed>}
 */
function partition(callable $predicate): Closure
{
    return static function (iterable $it) use ($predicate): array {
        $truthy = [];
        $falsy = [];

        foreach ($it as $k => $v) {
            if ($predicate($v)) {
                $truthy[$k] = $v;
            } else {
                $falsy[$k] = $v;
            }
        }

        return [$truthy, $falsy];
    };
}
