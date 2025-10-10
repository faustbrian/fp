<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function preg_match_all;

/**
 * Creates a closure that performs global regex pattern matching on a string.
 *
 * Returns a pipeable function that executes preg_match_all with the specified
 * pattern, finding all occurrences of the pattern in the input string. The
 * matches array structure depends on the flags parameter, with PREG_SET_ORDER
 * providing row-based results and default behavior providing column-based results.
 *
 * ```php
 * $findDigits = matchAll('/\d+/');
 * $findDigits('Item 123 costs $45'); // [[0 => ['123', '45']]]
 *
 * // With PREG_SET_ORDER for row-based results
 * $findTags = matchAll('/<(\w+)>/', PREG_SET_ORDER);
 * $findTags('<div><span></span></div>');
 * // [[0 => '<div>', 1 => 'div'], [0 => '<span>', 1 => 'span'], ...]
 *
 * // Using in a pipeline to extract email domains
 * pipe(
 *     'Contact: admin@example.com, support@test.org',
 *     matchAll('/@([\w.]+)/'),
 *     fn($m) => $m[1] ?? []
 * ); // ['example.com', 'test.org']
 * ```
 *
 * @param  string  $pattern PCRE regex pattern to match against the input string.
 *                          Must include delimiters and can include modifiers
 *                          like /pattern/i for case-insensitive matching.
 * @param  int     $flags   Optional PREG_* flags to modify matching behavior.
 *                          Common flags: PREG_PATTERN_ORDER (default), PREG_SET_ORDER,
 *                          PREG_OFFSET_CAPTURE for position tracking.
 * @param  int     $offset  Optional character offset to start matching from.
 *                          Useful for iterative matching or skipping known content.
 * @return Closure Returns a closure that accepts a string and returns an array
 *                 of all pattern matches, or empty array if no matches found
 */
function matchAll(string $pattern, int $flags = 0, int $offset = 0): Closure
{
    return static function (string $s) use ($pattern, $flags, $offset): array {
        $matches = [];
        preg_match_all($pattern, $s, $matches, $flags, $offset);

        return $matches;
    };
}
