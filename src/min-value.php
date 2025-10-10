<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

/**
 * Finds the minimum value from an iterable collection of numeric values.
 *
 * Iterates through all values and returns the smallest numeric value found.
 * Returns null for empty collections, making it safe to use with any iterable
 * without requiring pre-validation.
 *
 * ```php
 * minValue([5, 2, 9, 1, 7]); // 1
 * minValue([3.14, 2.71, 1.41]); // 1.41
 * minValue([]); // null
 * ```
 *
 * @param  iterable<float|int> $values Collection of numeric values to compare. Can be any
 *                                     iterable type including arrays, generators, or iterators.
 *                                     Non-numeric values will cause type errors.
 * @return null|float|int      The minimum value found, or null if the iterable is empty
 */
function minValue(iterable $values): int|float|null
{
    $min = null;

    foreach ($values as $value) {
        if ($min === null || $value < $min) {
            $min = $value;
        }
    }

    return $min;
}
