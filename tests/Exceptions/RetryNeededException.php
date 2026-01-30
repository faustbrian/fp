<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Exceptions;

use Exception;

/**
 * Thrown to indicate a retry is needed during testing.
 *
 * This exception is used in tests to simulate conditional failures that
 * should trigger a retry, allowing verification that retry mechanisms
 * correctly respond to transient failures.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class RetryNeededException extends Exception
{
    /**
     * Creates a new exception instance for triggering a retry.
     *
     * @return self A new exception instance indicating a retry is needed
     */
    public static function create(): self
    {
        return new self('Retry');
    }
}
