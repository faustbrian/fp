<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_slice;
use function array_values;
use function is_array;

/**
 * Returns the last element from an array or iterable.
 *
 * For arrays, efficiently uses array_slice to get the last element.
 * For other iterables, iterates through and returns the final value.
 * Returns null if the collection is empty.
 *
 * ```php
 * last([1, 2, 3, 4]); // 4
 * last(['a']); // 'a'
 * last([]); // null
 * ```
 *
 * @param  iterable<mixed> $it The iterable to get the last element from
 * @return mixed           The last element or null if empty
 */
function last(iterable $it): mixed
{
    if (is_array($it)) {
        if ([] === $it) {
            return null;
        }

        return array_values(array_slice($it, -1))[0] ?? null;
    }

    $last = null;

    foreach ($it as $v) {
        $last = $v;
    }

    return $last;
}
