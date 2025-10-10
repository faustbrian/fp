<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function preg_split;

/**
 * Splits a string by a regular expression pattern in a pipeable manner.
 *
 * Returns a closure that divides a string into an array of substrings using preg_split with
 * the specified pattern, limit, and flags. Supports all preg_split functionality including
 * capture groups, limit controls, and special flags for advanced splitting behavior.
 *
 * Returns false if the pattern is invalid or an error occurs during splitting. Common flags
 * include PREG_SPLIT_NO_EMPTY to remove empty strings, PREG_SPLIT_DELIM_CAPTURE to include
 * delimiter matches, and PREG_SPLIT_OFFSET_CAPTURE to include string offsets.
 *
 * ```php
 * $words = pipe(
 *     'hello, world, foo',
 *     split('/,\s*\//')
 * ); // ['hello', 'world', 'foo']
 *
 * $limited = pipe(
 *     'one:two:three:four',
 *     split('/:/', 2)
 * ); // ['one', 'two:three:four']
 * ```
 *
 * @param  string  $pattern Regular expression pattern to split by, following PCRE syntax with
 *                          delimiters. The pattern determines where the string is divided.
 * @param  int     $limit   Maximum number of substrings to return. -1 (default) means no limit.
 *                          If positive, returns at most that many elements with the remainder in the last element.
 * @param  int     $flags   bitmask of PREG_SPLIT_* constants to control splitting behavior such as
 *                          capturing delimiters, removing empty results, or including string offsets
 * @return Closure A closure with signature (string $s): array|false that splits the input string
 *                 by the pattern and returns the resulting array, or false on pattern error
 */
function split(string $pattern, int $limit = -1, int $flags = 0): Closure
{
    return static fn (string $s): array|false => preg_split($pattern, $s, $limit, $flags);
}
