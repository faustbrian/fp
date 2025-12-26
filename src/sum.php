<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

/**
 * Calculates the sum of all numeric values in an iterable.
 *
 * Iterates through all values and accumulates their sum. Handles both
 * integer and float values, returning the appropriate numeric type based
 * on the input values.
 *
 * ```php
 * sum([1, 2, 3, 4]); // Returns: 10
 * sum([1.5, 2.5, 3.0]); // Returns: 7.0
 * sum(new ArrayIterator([10, 20, 30])); // Returns: 60
 * ```
 *
 * @param  iterable<float|int> $values The numeric values to sum. Can be an array
 *                                     or any iterable containing integers or floats.
 *                                     Non-numeric values will cause type errors.
 * @return float|int           The total sum of all values. Returns integer if all values
 *                             are integers, otherwise returns float if any value is a float.
 */
function sum(iterable $values): int|float
{
    $total = 0;

    foreach ($values as $value) {
        $total += $value;
    }

    return $total;
}
