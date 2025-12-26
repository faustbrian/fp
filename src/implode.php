<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

/**
 * Creates a curried implode function that joins array elements with a specified separator.
 *
 * Returns a function that accepts an array and joins its elements into a single string
 * using the predefined glue string. This curried version is useful for functional pipelines
 * and array mapping operations where the separator remains constant.
 *
 * ```php
 * $tags = [
 *     ['php', 'laravel', 'mysql'],
 *     ['javascript', 'react', 'nodejs'],
 *     ['python', 'django', 'postgresql']
 * ];
 *
 * $joinWithComma = implode(', ');
 * $tagStrings = array_map($joinWithComma, $tags);
 * // ['php, laravel, mysql', 'javascript, react, nodejs', 'python, django, postgresql']
 * ```
 *
 * @param  string                 $glue The separator string inserted between each array element
 * @return Closure(array): string Returns a function accepting an array that produces
 *                                a string with all elements joined by the glue
 */
function implode(string $glue): Closure
{
    return static fn (array $a): string => \implode($glue, $a);
}
