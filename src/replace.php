<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function str_replace;

/**
 * Replaces all occurrences of search strings with replacement strings in a pipeable manner.
 *
 * Returns a closure that performs string replacement using str_replace, making it compatible
 * with pipe-based functional composition. Supports both single and multiple simultaneous
 * replacements through array parameters.
 *
 * ```php
 * $cleaned = pipe(
 *     'hello world',
 *     replace('world', 'universe')
 * ); // 'hello universe'
 *
 * $normalized = pipe(
 *     'foo-bar_baz',
 *     replace(['-', '_'], ' ')
 * ); // 'foo bar baz'
 * ```
 *
 * @param  array<int|string, mixed>|string $find    Single string or array of strings to search for
 *                                                  in the input. Array keys are ignored.
 * @param  array<int|string, mixed>|string $replace Single string or array of replacement strings.
 *                                                  If array, replacements correspond by index to $find array.
 * @return Closure                         A closure with signature (string $s): string that performs
 *                                         the configured string replacement operation
 */
function replace(array|string $find, array|string $replace): Closure
{
    return static fn (string $s): string => str_replace($find, $replace, $s);
}
