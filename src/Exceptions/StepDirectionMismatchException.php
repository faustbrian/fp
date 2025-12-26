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
 * Thrown when a step value direction conflicts with the start/end range.
 *
 * This exception indicates that a range operation received a step value whose
 * direction (positive or negative) is incompatible with the start and end values.
 * For example, attempting to count from 1 to 10 with a negative step, or from
 * 10 to 1 with a positive step would trigger this exception.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class StepDirectionMismatchException extends InvalidArgumentException implements FpException
{
    /**
     * Creates a new exception instance for step direction mismatch.
     *
     * @return self a new exception instance indicating that the step value's
     *              direction (positive/negative) does not align with the range
     *              direction implied by the start and end values, making the
     *              range operation impossible to complete
     */
    public static function create(): self
    {
        return new self('Step direction does not match start/end range');
    }
}
