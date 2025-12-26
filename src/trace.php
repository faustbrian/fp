<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function var_dump;

/**
 * Dumps a value for debugging and returns it unchanged.
 *
 * Outputs the value using var_dump() to display its type and structure,
 * then returns the original value. This allows inserting debug output
 * into pipelines or function chains without disrupting the data flow.
 *
 * Unlike tap(), which requires a callable, trace() provides immediate
 * debugging output with no configuration needed.
 *
 * ```php
 * $result = trace(42); // Dumps: int(42), returns 42
 *
 * // In a pipeline
 * $value = pipe(
 *     [1, 2, 3],
 *     map(fn($x) => $x * 2),
 *     trace, // Dumps array, returns it unchanged
 *     sum()
 * );
 * ```
 *
 * @param  mixed $arg The value to dump and return. Can be any type including
 *                    arrays, objects, resources, or scalar values. The value
 *                    is passed to var_dump() for detailed type inspection.
 * @return mixed the original value unchanged, allowing trace() to be used
 *               inline without breaking data flow or pipelines
 */
function trace(mixed $arg): mixed
{
    var_dump($arg);

    return $arg;
}
