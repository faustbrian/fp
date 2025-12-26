<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use const STR_PAD_LEFT;

use function mb_str_pad;

/**
 * Creates a curried function for left-padding strings to a specified length.
 *
 * Returns a function that pads strings on the left side using the specified
 * padding string until the target length is reached. Uses multibyte-safe padding
 * via mb_str_pad() to correctly handle Unicode characters. Useful in pipelines
 * for formatting strings with consistent alignment.
 *
 * ```php
 * $pad10 = padLeft(10);
 * $pad10('hello'); // '     hello'
 * $pad10('hi'); // '        hi'
 *
 * $padZeros = padLeft(8, '0');
 * $padZeros('42'); // '00000042'
 *
 * // In pipelines
 * $formatted = pipe(
 *     $numbers,
 *     map(padLeft(5, '0'))
 * );
 * ```
 *
 * @param  int     $length    The total length of the resulting string. If the input string
 *                            is already longer than this length, it will be returned unchanged.
 * @param  string  $padString The string to use for padding. Defaults to a single space.
 *                            Can be multiple characters which will be repeated as needed.
 * @return Closure A curried function that accepts a string and returns the left-padded
 *                 result using multibyte-safe string operations
 */
function padLeft(int $length, string $padString = ' '): Closure
{
    return static fn (string $s): string => mb_str_pad($s, $length, $padString, STR_PAD_LEFT);
}
