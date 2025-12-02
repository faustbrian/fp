<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

/**
 * Returns the first element of an array or null if the array is empty.
 *
 * Provides safe access to the first element without requiring array index checks.
 * Unlike array_shift, this function does not modify the original array and does
 * not reset array keys. Always accesses the zero index regardless of array keys.
 *
 * ```php
 * $numbers = [1, 2, 3, 4];
 * $first = head($numbers); // 1
 *
 * $empty = [];
 * $nothing = head($empty); // null
 *
 * $associative = ['a' => 1, 'b' => 2];
 * $firstValue = head($associative); // 1
 * ```
 *
 * @param  array<mixed> $a The array to extract the first element from
 * @return mixed        The first element at index 0, or null if the array is empty
 */
function head(array $a): mixed
{
    return $a[0] ?? null;
}
