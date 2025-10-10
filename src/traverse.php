<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function Cline\fp\map;
use function Cline\fp\sequence;

/**
 * Maps a function over a structure then sequences the results.
 *
 * Traverse combines map and sequence in one operation. It maps a function
 * that returns wrapped values over a collection, then inverts the nesting
 * structure, transforming an array of wrapped values into a wrapped array.
 *
 * This is a fundamental operation for working with effects in a functional
 * style, allowing you to map effectful computations and collect the results.
 *
 * ```php
 * // Map function that wraps each value in an array
 * $duplicate = fn($x) => [$x, $x];
 * $traverseDuplicate = traverse($duplicate);
 *
 * $traverseDuplicate([1, 2]);
 * // [[1, 2], [1, 2]]
 *
 * // Simpler example - wrap each element, then transpose
 * $wrap = fn($x) => [$x];
 * $traverseWrap = traverse($wrap);
 * $traverseWrap([1, 2, 3]);
 * // [[1, 2, 3]]
 * ```
 *
 * @param  callable              $fn Function that maps values to wrapped values
 * @return Closure(array): array Function accepting array and returning sequenced result
 *
 * @see map() For mapping without sequencing
 * @see sequence() For sequencing without mapping
 */
function traverse(callable $fn): Closure
{
    return static function (array $array) use ($fn): array {
        $mapped = map($fn)($array);
        /** @var array<int, array<mixed>> $mapped */

        return sequence($mapped);
    };
}
