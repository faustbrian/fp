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
 * Creates a curried function for splitting strings by a delimiter.
 *
 * Returns a function that accepts a string and splits it into an array using the
 * specified delimiter. This curried version is useful for functional composition,
 * enabling delimiter-specific splitting functions that can be reused or composed
 * with other string operations.
 *
 * ```php
 * $splitByComma = explode(',');
 * $splitByComma('a,b,c'); // ['a', 'b', 'c']
 *
 * $splitLines = explode("\n");
 * $lines = array_map($splitLines, $multilineStrings);
 * ```
 *
 * @param  non-empty-string $delimiter The boundary string used to split the input.
 *                                     Must be a non-empty string. Common delimiters
 *                                     include ',', '|', '\n', or custom separators.
 * @return Closure          A function that accepts a string and returns an array of substrings
 *                          created by splitting the input at each occurrence of the delimiter.
 *                          Empty segments between consecutive delimiters are preserved.
 */
function explode(string $delimiter): Closure
{
    return static fn (string $s): array => \explode($delimiter, $s);
}
