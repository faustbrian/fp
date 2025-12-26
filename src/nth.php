<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

/**
 * Generates the nth element in a sequence by repeatedly applying a mapper function.
 *
 * Starting with an initial value, applies the mapper function (count - 1) times
 * to generate the nth element in a sequence. The initial value itself is considered
 * the first element (n=1), so requesting the 1st element returns the initial value
 * unchanged. This is useful for generating sequences, computing factorial-like
 * operations, or simulating iterative processes.
 *
 * ```php
 * // Generate the 5th power of 2
 * nth(5, 2, fn($n) => $n * 2); // 32 (2 → 4 → 8 → 16 → 32)
 *
 * // Calculate factorial-like sequences
 * nth(4, 1, fn($n) => $n * 2); // 8 (1 → 2 → 4 → 8)
 *
 * // First element returns initial value unchanged
 * nth(1, 10, fn($n) => $n + 5); // 10
 * ```
 *
 * @internal Uses an inlined while loop for performance instead of iterate() or reduce()
 *
 * @param  int      $count  The position in the sequence to generate (1-indexed). Values less
 *                          than 1 will return the result of applying the mapper to the initial
 *                          value due to the loop condition.
 * @param  mixed    $init   The initial value to start the sequence. This is considered the
 *                          first element (n=1) and is returned unchanged when count is 1.
 * @param  callable $mapper The transformation function to apply repeatedly. Receives the
 *                          previous value and returns the next value in the sequence.
 *                          Called exactly (count - 1) times for count >= 1.
 * @return mixed    The nth element in the generated sequence after applying the mapper
 *                  function the appropriate number of times
 */
function nth(int $count, mixed $init, callable $mapper): mixed
{
    while (--$count > 0) {
        $init = $mapper($init);
    }

    return $init;
}
