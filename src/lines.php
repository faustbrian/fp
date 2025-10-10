<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function preg_split;

/**
 * Splits a string into an array of lines.
 *
 * Splits on various newline characters (\n, \r\n, \r) and returns an array
 * of lines. Empty lines are preserved. Useful for processing multi-line
 * text, file contents, or text blocks.
 *
 * ```php
 * lines("hello\nworld"); // ['hello', 'world']
 * lines("foo\r\nbar\rbaz"); // ['foo', 'bar', 'baz']
 * lines("one\n\ntwo"); // ['one', '', 'two']
 * ```
 *
 * @param  string        $str The string to split into lines
 * @return array<string> Array of lines
 */
function lines(string $str): array
{
    return preg_split('/\r\n|\r|\n/', $str) ?: [];
}
