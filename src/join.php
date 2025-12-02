<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function Cline\fp\implode;

/**
 * Returns a higher-order function that joins array elements into a string using a separator.
 *
 * Provides the conventional functional programming name for implode(). Creates a curried
 * function that joins array elements into a single string using the specified separator.
 * The glue string is inserted between each element, and array values are coerced to strings.
 *
 * This curried form enables partial application, making it ideal for composition in
 * functional pipelines where the separator is known but the data varies.
 *
 * ```php
 * // Create reusable joiners
 * $joinComma = join(', ');
 * $joinComma(['a', 'b', 'c']); // 'a, b, c'
 *
 * // Use in pipelines
 * $format = pipe(
 *     map('strtoupper'),
 *     join(' - ')
 * );
 * $format(['foo', 'bar']); // 'FOO - BAR'
 *
 * // Join with newlines
 * $joinLines = join("\n");
 * $joinLines(['Line 1', 'Line 2']); // "Line 1\nLine 2"
 * ```
 *
 * @param  string                 $glue The separator string inserted between each array element.
 *                                      Can be empty string to concatenate without separator.
 * @return Closure(array): string a curried function that accepts an array and returns a string
 *                                with all elements joined by the glue separator
 *
 * @see implode() For the underlying implementation and detailed behavior.
 */
function join(string $glue): Closure
{
    return implode($glue);
}
