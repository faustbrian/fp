<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function Cline\fp\afilter;

/**
 * Standard functional programming filter operation.
 *
 * Alias for afilter() providing the conventional FP name. Filters an iterable
 * using a predicate function. Returns a new array containing only elements
 * where the predicate returns true. Keys are preserved.
 *
 * ```php
 * $isEven = filter(fn($x) => $x % 2 === 0);
 * $result = $isEven([1, 2, 3, 4]); // [1 => 2, 3 => 4]
 *
 * $notNull = filter(fn($x) => $x !== null);
 * $result = $notNull([1, null, 3]); // [0 => 1, 2 => 3]
 * ```
 *
 * @param  null|callable(mixed): bool                 $c Predicate function receiving value and returning bool, or null to remove falsy values
 * @return Closure(iterable): array<array-key, mixed> Curried function accepting an iterable and returning filtered array with preserved keys
 *
 * @see afilter() For the underlying implementation
 * @since 1.0.0
 */
function filter(?callable $c = null): Closure
{
    return afilter($c);
}
