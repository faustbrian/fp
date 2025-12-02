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
 * Splits a string on various newline characters (\n, \r\n, \r) and returns an indexed
 * array of lines. The function handles all common line ending formats across different
 * operating systems (Unix/Linux: \n, Windows: \r\n, Classic Mac: \r).
 *
 * Empty lines are preserved in the output, making this suitable for processing structured
 * text where blank lines have semantic meaning. Returns an empty array if the input string
 * is empty.
 *
 * ```php
 * // Basic line splitting
 * lines("hello\nworld"); // ['hello', 'world']
 *
 * // Handle different line endings
 * lines("foo\r\nbar\rbaz"); // ['foo', 'bar', 'baz']
 *
 * // Preserve empty lines
 * lines("one\n\ntwo"); // ['one', '', 'two']
 *
 * // Process file contents
 * $content = file_get_contents('data.txt');
 * $lineArray = lines($content);
 *
 * // Use in functional pipelines
 * $processLog = pipe(
 *     lines(...),
 *     map('trim'),
 *     filter(fn($line) => str_starts_with($line, 'ERROR'))
 * );
 * ```
 *
 * @param  string        $str The string to split into individual lines. Can contain any
 *                            combination of \n, \r\n, or \r line endings.
 * @return array<string> An indexed array of strings, one for each line in the input.
 *                       Empty lines are preserved as empty strings in the array.
 */
function lines(string $str): array
{
    return preg_split('/\r\n|\r|\n/', $str) ?: [];
}
