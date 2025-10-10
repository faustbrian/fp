<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function Cline\fp\implode;

/**
 * Standard functional programming join operation.
 *
 * Alias for implode() providing the conventional FP name. Creates a curried
 * function that joins array elements into a single string using the specified
 * separator.
 *
 * ```php
 * $joinComma = join(', ');
 * $joinComma(['a', 'b', 'c']); // 'a, b, c'
 * ```
 *
 * @param  string  $glue The separator string inserted between elements
 * @return Closure Function accepting array and returning joined string
 *
 * @see implode() For the underlying implementation
 */
function join(string $glue): Closure
{
    return implode($glue);
}
