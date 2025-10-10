<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use const PREG_SPLIT_NO_EMPTY;

use function mb_trim;
use function preg_split;

/**
 * Splits a string into an array of words.
 *
 * Splits on whitespace (spaces, tabs, newlines) and returns an array of
 * words. Empty strings from multiple consecutive spaces are filtered out.
 * Useful for text processing, word counting, or parsing simple formats.
 *
 * ```php
 * words("hello world"); // ['hello', 'world']
 * words("foo  bar\tbaz"); // ['foo', 'bar', 'baz']
 * words("one\ntwo  three"); // ['one', 'two', 'three']
 * ```
 *
 * @param  string        $str The string to split into words
 * @return array<string> Array of words
 */
function words(string $str): array
{
    return preg_split('/\s+/', mb_trim($str), -1, PREG_SPLIT_NO_EMPTY) ?: [];
}
