<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function mb_rtrim;

/**
 * Removes whitespace or specified characters from the end of a string in a pipeable manner.
 *
 * Returns a closure that strips characters from the right side of a string using multibyte-safe
 * rtrim. When no characters are specified, removes standard whitespace characters (spaces, tabs,
 * newlines, carriage returns, null bytes, and vertical tabs).
 *
 * ```php
 * $trimmed = pipe(
 *     'hello world   ',
 *     rtrim()
 * ); // 'hello world'
 *
 * $cleaned = pipe(
 *     'data###',
 *     rtrim('#')
 * ); // 'data'
 * ```
 *
 * @param  null|string $characters Optional string containing characters to strip from the end.
 *                                 If null, removes standard whitespace characters.
 *                                 Each character in this string will be stripped.
 * @return Closure     A closure with signature (string $s): string that removes the specified
 *                     characters from the end of the input string
 */
function rtrim(?string $characters = null): Closure
{
    return static fn (string $s): string => $characters !== null
        ? mb_rtrim($s, $characters)
        : mb_rtrim($s);
}
