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
 * Monadic bind operation - maps and flattens in one step.
 *
 * The bind operation (also known as flatMap or chain) is fundamental to
 * monadic composition. It applies a function that returns a wrapped value
 * to each element, then flattens the result by one level, preventing
 * nested structures.
 *
 * In category theory, bind is the monadic composition operator that
 * allows sequencing of computations within a monadic context.
 *
 * ```php
 * $duplicate = bind(fn($x) => [$x, $x]);
 * $duplicate([1, 2, 3]); // [1, 1, 2, 2, 3, 3]
 *
 * $parseWords = bind(fn($s) => explode(' ', $s));
 * $parseWords(['hello world', 'foo bar']); // ['hello', 'world', 'foo', 'bar']
 * ```
 *
 * @see flatMap() For the underlying implementation
 * @see chain() For an alias with the same behavior
 * @param  callable                 $fn Function returning wrapped value for each element
 * @return Closure(iterable): array Function accepting iterable and returning flattened result
 */
function bind(callable $fn): Closure
{
    return flatMap($fn);
}
