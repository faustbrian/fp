<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function Cline\fp\amap;

/**
 * Standard functional programming map operation.
 *
 * Alias for amap() providing the conventional FP name. Maps an iterable using
 * a transformation function that receives only values. Keys are preserved in
 * the resulting array.
 *
 * ```php
 * $double = map(fn($x) => $x * 2);
 * $result = $double([1, 2, 3]); // [2, 4, 6]
 *
 * $uppercase = map('strtoupper');
 * $result = $uppercase(['foo', 'bar']); // ['FOO', 'BAR']
 * ```
 *
 * @see amap() For the underlying implementation
 * @param  callable(mixed): mixed   $c Transformation function receiving value and returning transformed value
 * @return Closure(iterable): array Function accepting an iterable and returning transformed array
 */
function map(callable $c): Closure
{
    return amap($c);
}
