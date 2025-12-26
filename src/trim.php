<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function mb_trim;

/**
 * Creates a function that removes whitespace or specified characters from both ends of a string.
 *
 * Returns a curried function that trims strings using multibyte-safe operations.
 * When no characters are specified, removes standard whitespace (spaces, tabs,
 * newlines, carriage returns, null bytes, vertical tabs). When characters are
 * provided, removes any of those characters from both ends.
 *
 * Uses mb_trim() internally for proper Unicode support, ensuring correct
 * handling of multibyte characters in UTF-8 and other encodings.
 *
 * ```php
 * $trimmer = trim();
 * $trimmer("  hello  "); // Returns: "hello"
 * $trimmer("\n\ttest\r\n"); // Returns: "test"
 *
 * $trimSlashes = trim('/');
 * $trimSlashes("/path/to/file/"); // Returns: "path/to/file"
 *
 * // In a pipeline
 * $cleaned = pipe(
 *     "  John Doe  ",
 *     trim(),
 *     strtolower
 * ); // Returns: "john doe"
 * ```
 *
 * @param  null|string $characters Optional list of characters to remove from both ends.
 *                                 Each character in the string will be stripped. When
 *                                 null (default), removes all standard whitespace characters.
 *                                 For example, "/_" removes forward slashes and underscores.
 * @return Closure     A function that accepts a string and returns the trimmed result.
 *                     The function uses multibyte-safe trimming to properly handle
 *                     Unicode characters across different encodings.
 */
function trim(?string $characters = null): Closure
{
    return static fn (string $s): string => $characters !== null
        ? mb_trim($s, $characters)
        : mb_trim($s);
}
