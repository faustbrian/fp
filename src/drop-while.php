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
 * Creates a function that drops elements from an iterable while a predicate holds true.
 *
 * Returns a curried function that processes an iterable, skipping elements from the
 * beginning as long as the predicate returns true. Once the predicate returns false
 * for any element, all remaining elements (including that element) are collected and
 * returned. The original keys are preserved in the result. Useful for skipping header
 * rows, filtering out leading invalid data, or implementing take-after semantics.
 *
 * ```php
 * $dropNegatives = dropWhile(fn($x) => $x < 0);
 * $dropNegatives([-3, -1, 0, 2, -5, 4]); // [0, 2, -5, 4]
 *
 * $skipUntilValid = dropWhile(fn($item) => !$item['valid']);
 * $skipUntilValid($data); // Returns all items starting from first valid one
 * ```
 *
 * @param  callable(mixed): bool $predicate A function that receives each value and returns
 *                                          true to drop the element or false to begin collecting.
 *                                          Once it returns false, all subsequent elements are
 *                                          included regardless of predicate results.
 * @return Closure               a function that accepts an iterable and returns an array containing
 *                               all elements from the first element where the predicate returned false
 *                               onwards, preserving original keys
 */
function dropWhile(callable $predicate): Closure
{
    return static function (iterable $it) use ($predicate): array {
        $result = [];
        $dropping = true;

        foreach ($it as $k => $v) {
            if ($dropping && $predicate($v)) {
                continue;
            }

            $dropping = false;
            $result[$k] = $v;
        }

        return $result;
    };
}
