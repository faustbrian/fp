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
 * Constrains a numeric value to stay within a specified range.
 *
 * Returns a closure that clamps any given value between minimum and maximum
 * bounds. Values below the minimum are set to the minimum, values above the
 * maximum are set to the maximum, and values within range pass through unchanged.
 * This is a curried function enabling partial application in functional pipelines.
 * Commonly used for constraining user input, normalizing values, or ensuring
 * data integrity within valid ranges.
 *
 * ```php
 * $clampScore = clamp(0, 100);
 * $clampScore(150); // 100
 * $clampScore(-10); // 0
 * $clampScore(50); // 50
 *
 * // Constrain volume levels
 * $clampVolume = clamp(0, 10);
 * array_map($clampVolume, [-5, 3, 8, 15]); // [0, 3, 8, 10]
 *
 * // Ensure valid RGB values
 * $clampRGB = clamp(0, 255);
 * $clampRGB($userInput); // Always between 0-255
 * ```
 *
 * @param  float|int $min Lower bound. Values below this are clamped to this minimum.
 *                        Can be any numeric value including negative numbers.
 * @param  float|int $max Upper bound. Values above this are clamped to this maximum.
 *                        Should be greater than or equal to $min for logical ranges.
 * @return Closure   Returns a closure accepting a numeric value and returning the
 *                   value constrained to the range [min, max]. The return type matches
 *                   the input type (int or float).
 */
function clamp(int|float $min, int|float $max): Closure
{
    return static fn (int|float $value): int|float => match (true) {
        $value < $min => $min,
        $value > $max => $max,
        default => $value,
    };
}
