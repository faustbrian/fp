<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

/**
 * Calculates the product of all numeric values in an iterable.
 *
 * Multiplies all values together starting from an initial value of 1,
 * returning the final product. Works with any iterable including arrays,
 * generators, and iterator objects. The result type (int or float) depends
 * on the input values and intermediate calculations.
 *
 * ```php
 * $result = product([2, 3, 4]); // 24
 * $result = product([1.5, 2, 3]); // 9.0
 * $result = product([]); // 1 (identity value for multiplication)
 * $result = product([5]); // 5
 * ```
 *
 * @param  iterable  $values An iterable collection of numeric values to multiply together.
 *                           Empty iterables return 1 (the multiplicative identity).
 * @return float|int The product of all values. Returns int if all intermediate results
 *                   are integers, otherwise returns float.
 */
function product(iterable $values): int|float
{
    $total = 1;

    foreach ($values as $value) {
        $total *= $value;
    }

    return $total;
}
