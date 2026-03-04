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
 * Filters an iterable by rejecting elements that match the predicate.
 *
 * Opposite of filter() - keeps only elements where the predicate returns
 * false. Returns a curried function that accepts an iterable and returns
 * an array with keys preserved containing only rejected elements.
 *
 * ```php
 * $rejectEven = reject(fn($x) => $x % 2 === 0);
 * $rejectEven([1, 2, 3, 4]); // [0 => 1, 2 => 3]
 *
 * $rejectNull = reject(fn($x) => $x === null);
 * $rejectNull([1, null, 3]); // [0 => 1, 2 => 3]
 * ```
 *
 * @param  callable                 $predicate Function that returns true for elements to reject
 * @return Closure(iterable): array Function accepting iterable and returning filtered array
 */
function reject(callable $predicate): Closure
{
    return static function (iterable $it) use ($predicate): array {
        $result = [];

        foreach ($it as $k => $v) {
            if ($predicate($v)) {
                continue;
            }

            /** @var int|string $k */
            $result[$k] = $v;
        }

        return $result;
    };
}
