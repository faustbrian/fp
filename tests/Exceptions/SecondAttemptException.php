<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Exceptions;

use LogicException;

/**
 * Thrown to simulate a second attempt failure during retry testing.
 *
 * This exception is used in tests to simulate failures on the second attempt,
 * allowing verification that retry mechanisms correctly handle different
 * exception types across multiple attempts.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class SecondAttemptException extends LogicException
{
    /**
     * Creates a new exception instance for the second attempt failure.
     *
     * @return self A new exception instance indicating the second attempt failed
     */
    public static function create(): self
    {
        return new self('Second error');
    }
}
