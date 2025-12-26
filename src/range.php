<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Cline\fp\Exceptions\StepDirectionMismatchException;
use Cline\fp\Exceptions\ZeroStepException;

use function throw_if;

/**
 * Generates a numeric sequence from start to end with optional step increment.
 *
 * Creates an array of numbers progressing from the start value to the end value
 * (inclusive), incrementing by the step amount. Supports both ascending and
 * descending sequences with positive and negative steps. Validates that the step
 * direction matches the start/end relationship to prevent infinite loops.
 *
 * This function shadows PHP's built-in range() to provide stricter validation
 * and prevent common errors. To use PHP's native range(), call \range() with
 * a leading backslash.
 *
 * ```php
 * $ascending = range(1, 5); // [1, 2, 3, 4, 5]
 * $descending = range(5, 1, -1); // [5, 4, 3, 2, 1]
 * $decimals = range(0, 1, 0.25); // [0, 0.25, 0.5, 0.75, 1.0]
 * $byTwo = range(0, 10, 2); // [0, 2, 4, 6, 8, 10]
 * ```
 *
 * @param float|int $start starting value for the sequence
 * @param float|int $end   Ending value for the sequence (inclusive). The last element may equal
 *                         or be less than/greater than this value depending on step alignment.
 * @param float|int $step  Increment or decrement value for each step in the sequence. Must be
 *                         non-zero and directionally consistent with start/end (positive for
 *                         ascending, negative for descending). Default: 1.
 *
 * @throws ZeroStepException              when step is zero (would cause infinite loop)
 * @throws StepDirectionMismatchException When step direction conflicts with start/end range
 *                                        (e.g., positive step with descending range).
 *
 * @return array<int, float|int> an indexed array containing the generated numeric sequence
 *
 * @api
 */
function range(int|float $start, int|float $end, int|float $step = 1): array
{
    throw_if($step === 0 || $step === 0.0, ZeroStepException::create());

    throw_if(($end > $start && $step < 0) || ($end < $start && $step > 0), StepDirectionMismatchException::create());

    $result = [];

    if ($step > 0) {
        for ($i = $start; $i <= $end; $i += $step) {
            $result[] = $i;
        }
    } else {
        for ($i = $start; $i >= $end; $i += $step) {
            $result[] = $i;
        }
    }

    return $result;
}
