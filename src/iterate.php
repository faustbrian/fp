<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Generator;

/**
 * Generates an infinite sequence by repeatedly applying a function to its previous result.
 *
 * This function creates a lazy, infinite generator that starts with an initial value
 * and produces subsequent values by repeatedly applying the mapper function to the
 * previous result. The initial value is yielded first, followed by mapper($init),
 * mapper(mapper($init)), and so on indefinitely.
 *
 * **IMPORTANT:** This generator produces an infinite sequence. You must use a termination
 * condition (such as ittake(), array_slice(), or a break statement) to avoid infinite loops.
 *
 * ```php
 * // Generate powers of 2: [1, 2, 4, 8, 16, ...]
 * $powers = iterate(1, fn($n) => $n * 2);
 * foreach (ittake(5)($powers) as $value) {
 *     echo $value; // Outputs: 1, 2, 4, 8, 16
 * }
 *
 * // Generate incrementing sequence: [0, 1, 2, 3, ...]
 * $sequence = iterate(0, fn($n) => $n + 1);
 * ```
 *
 * @param  mixed                             $init   The initial value to start the sequence. This value is yielded
 *                                                   first before any transformations are applied.
 * @param  callable(mixed): mixed            $mapper A function that transforms one element into the next.
 *                                                   Receives the previous value and returns the next value
 *                                                   in the infinite sequence.
 * @return Generator<int, mixed, void, void> an infinite generator yielding the initial value
 *                                           followed by successive applications of the mapper function
 *
 * @see ittake() For limiting the number of values consumed from the infinite sequence.
 */
function iterate(mixed $init, callable $mapper): Generator
{
    yield $init;

    // PHPStan doesn't like that this is an infinite loop, but it's
    // supposed to be an infinite loop.
    // @phpstan-ignore-next-line
    while (true) {
        yield $init = $mapper($init);
    }
}
