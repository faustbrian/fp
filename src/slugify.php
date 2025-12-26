<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function mb_strtolower;
use function mb_trim;
use function preg_replace;

/**
 * Converts a string to a URL-friendly slug with configurable separator.
 *
 * Returns a closure that transforms strings into URL-safe slugs by converting to lowercase,
 * replacing sequences of non-alphanumeric characters with separators, and trimming excess
 * separators from the ends. Commonly used for generating clean URLs, file names, or identifiers
 * from user input or titles.
 *
 * The transformation normalizes spacing and punctuation, collapses consecutive separators,
 * and produces ASCII-only output suitable for URLs. Non-ASCII characters are removed rather
 * than transliterated.
 *
 * ```php
 * $slug = pipe(
 *     'Hello World!',
 *     slugify()
 * ); // 'hello-world'
 *
 * $filename = pipe(
 *     'My Document (Draft).pdf',
 *     slugify('_')
 * ); // 'my_document_draft_pdf'
 * ```
 *
 * @param  string  $separator Character or string to use as separator between words, typically
 *                            a hyphen or underscore. Defaults to hyphen for URL-style slugs.
 *                            Consecutive non-alphanumeric characters are replaced with a single separator.
 * @return Closure A closure with signature (string $s): string that converts the input
 *                 to a lowercase, URL-safe slug using the configured separator
 */
function slugify(string $separator = '-'): Closure
{
    return static function (string $s) use ($separator): string {
        // Convert to lowercase
        $slug = mb_strtolower($s);

        // Replace non-alphanumeric characters with separator
        $slug = preg_replace('/[^a-z0-9]+/', $separator, $slug);

        // Remove leading/trailing separators
        return mb_trim($slug ?? '', $separator);
    };
}
