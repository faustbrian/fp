<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function mb_ltrim;

/**
 * Creates a closure that removes characters from the left side of a string.
 *
 * Returns a pipeable function that strips whitespace or specified characters
 * from the beginning of a string. Uses multibyte-safe trimming via mb_ltrim
 * to properly handle Unicode characters and various whitespace types including
 * zero-width spaces and non-breaking spaces.
 *
 * ```php
 * $trimStart = ltrim();
 * $trimStart('  hello  '); // 'hello  '
 *
 * $trimDashes = ltrim('-');
 * $trimDashes('---title---'); // 'title---'
 *
 * // Using in a pipeline
 * pipe(
 *     '  example  ',
 *     ltrim(),
 *     strtoupper()
 * ); // 'EXAMPLE  '
 * ```
 *
 * @param  null|string $characters Optional string containing characters to remove.
 *                                 If null, removes standard whitespace characters
 *                                 including spaces, tabs, newlines, and Unicode spaces.
 * @return Closure     Returns a closure that accepts a string and returns the
 *                     left-trimmed result with specified characters removed
 */
function ltrim(?string $characters = null): Closure
{
    return static fn (string $s): string => $characters !== null
        ? mb_ltrim($s, $characters)
        : mb_ltrim($s);
}
