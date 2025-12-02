<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp\Exceptions;

use InvalidArgumentException;

/**
 * Thrown when a zero step value is provided to a range operation.
 *
 * This exception indicates that a step value of zero was provided to a range
 * or iteration operation where a non-zero step is required to make progress.
 * A zero step would create an infinite loop, as the iteration would never
 * advance toward the end value.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class ZeroStepException extends InvalidArgumentException implements FpException
{
    /**
     * Creates a new exception instance for zero step value.
     *
     * @return self a new exception instance indicating that a step value of
     *              zero was provided, which would prevent the range operation
     *              from making progress and result in an infinite loop
     */
    public static function create(): self
    {
        return new self('Step cannot be zero');
    }
}
