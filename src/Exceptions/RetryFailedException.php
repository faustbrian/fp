<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp\Exceptions;

use RuntimeException;

/**
 * Thrown when a retry operation completes without an exception to report.
 *
 * This exception indicates an edge case in retry logic where the retry mechanism
 * was expected to throw a captured exception after exhausting all retry attempts,
 * but no exception was recorded during the retry process. This typically occurs
 * when retry logic is misconfigured or when exception tracking fails.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class RetryFailedException extends RuntimeException implements FpException
{
    /**
     * Creates a new exception instance for missing retry exception.
     *
     * @return self a new exception instance indicating that the retry operation
     *              completed without capturing an exception to throw, which
     *              represents an unexpected state in the retry mechanism
     */
    public static function create(): self
    {
        return new self('No exception to throw');
    }
}
