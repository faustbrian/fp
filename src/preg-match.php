<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function preg_match;

/**
 * Creates a closure that performs regex pattern matching on strings.
 *
 * Returns a curried version of PHP's preg_match() that enables functional
 * composition and pipeline usage. The returned closure accepts a string and
 * returns the matches array on successful match, or null if the pattern does
 * not match. This makes regex matching composable with other functional utilities.
 *
 * The function wraps preg_match() behavior: a successful match (return value 1)
 * produces the matches array, while no match (return value 0) or error produces null.
 *
 * ```php
 * $extractEmail = pregMatch('/[\w\.-]+@[\w\.-]+\.\w+/');
 * $matches = $extractEmail('Contact: john@example.com'); // ['john@example.com']
 * $noMatch = $extractEmail('No email here'); // null
 *
 * $extractGroups = pregMatch('/^(\w+):(\d+)$/');
 * $result = $extractGroups('port:8080'); // ['port:8080', 'port', '8080']
 *
 * // Usage in pipeline
 * $validated = pipe(
 *     $input,
 *     pregMatch('/^\d{5}$/'),
 *     fn($m) => $m !== null
 * );
 * ```
 *
 * @param  string  $pattern Regular expression pattern to match against, including delimiters.
 *                          Must be a valid PCRE pattern (e.g., '/pattern/i').
 * @param  int     $flags   Optional PCRE flags to modify matching behavior (e.g., PREG_OFFSET_CAPTURE).
 *                          See PHP's preg_match() documentation for available flags. Default: 0.
 * @param  int     $offset  Position in the subject string to start searching from. Default: 0.
 * @return Closure a function accepting a string that returns the matches array if the pattern
 *                 matches, or null if no match is found or an error occurs
 */
function pregMatch(string $pattern, int $flags = 0, int $offset = 0): Closure
{
    return static function (string $s) use ($pattern, $flags, $offset): ?array {
        $matches = [];
        $result = preg_match($pattern, $s, $matches, $flags, $offset);

        return $result === 1 ? $matches : null;
    };
}
