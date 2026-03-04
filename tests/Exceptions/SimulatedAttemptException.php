<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Exceptions;

use RuntimeException;

use function sprintf;

/**
 * Thrown to simulate a failed attempt during retry testing.
 *
 * This exception is used in tests to simulate numbered failures in retry
 * operations, allowing verification that the retry mechanism correctly
 * tracks and reports attempt numbers.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class SimulatedAttemptException extends RuntimeException
{
    /**
     * Creates a new exception instance for a specific attempt number.
     *
     * @param  int  $attempt The attempt number that failed
     * @return self A new exception instance with a message indicating which attempt failed
     */
    public static function forAttempt(int $attempt): self
    {
        return new self(sprintf('Attempt %d failed', $attempt));
    }
}
