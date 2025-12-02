<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use const STR_PAD_RIGHT;

use function mb_str_pad;

/**
 * Creates a curried function for right-padding strings to a specified length.
 *
 * Returns a function that pads strings on the right side using the specified
 * padding string until the target length is reached. Uses multibyte-safe padding
 * via mb_str_pad() to correctly handle Unicode characters. Useful in pipelines
 * for formatting strings with consistent alignment and width.
 *
 * ```php
 * $pad10 = padRight(10);
 * $pad10('hello'); // 'hello     '
 * $pad10('hi'); // 'hi        '
 *
 * $padDots = padRight(15, '.');
 * $padDots('Name'); // 'Name...........'
 *
 * // In pipelines for tabular formatting
 * $formatted = pipe(
 *     $labels,
 *     map(padRight(20))
 * );
 * ```
 *
 * @param  int     $length    The total length of the resulting string. If the input string
 *                            is already longer than this length, it will be returned unchanged.
 * @param  string  $padString The string to use for padding. Defaults to a single space.
 *                            Can be multiple characters which will be repeated as needed.
 * @return Closure A curried function that accepts a string and returns the right-padded
 *                 result using multibyte-safe string operations
 */
function padRight(int $length, string $padString = ' '): Closure
{
    return static fn (string $s): string => mb_str_pad($s, $length, $padString, STR_PAD_RIGHT);
}
