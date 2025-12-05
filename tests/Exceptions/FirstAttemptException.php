<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Exceptions;

use InvalidArgumentException;

/**
 * Thrown to simulate a first attempt failure during retry testing.
 *
 * This exception is used in tests to simulate failures on the first attempt,
 * allowing verification that retry mechanisms correctly handle different
 * exception types across multiple attempts.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class FirstAttemptException extends InvalidArgumentException
{
    /**
     * Creates a new exception instance for the first attempt failure.
     *
     * @return self A new exception instance indicating the first attempt failed
     */
    public static function create(): self
    {
        return new self('First error');
    }
}
