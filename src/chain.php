<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function Cline\fp\flatMap;

/**
 * Monadic chain operation - maps and flattens in one step.
 *
 * Alias for flatMap/bind using the popular JavaScript naming convention.
 * Chains together computations that return wrapped values, automatically
 * flattening the result to prevent nested structures.
 *
 * ```php
 * $duplicate = chain(fn($x) => [$x, $x]);
 * $duplicate([1, 2, 3]); // [1, 1, 2, 2, 3, 3]
 * ```
 *
 * @param  callable                 $fn Function returning wrapped value for each element
 * @return Closure(iterable): array Function accepting iterable and returning flattened result
 *
 * @see flatMap() For the underlying implementation
 * @see bind() For the monadic bind operation
 */
function chain(callable $fn): Closure
{
    return flatMap($fn);
}
