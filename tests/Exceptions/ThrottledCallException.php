<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Exceptions;

use RuntimeException;

/**
 * Thrown when a throttled function call fails during testing.
 *
 * This exception is used in tests to simulate failures within throttled
 * functions, allowing verification that exception handling works correctly
 * with throttling decorators.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class ThrottledCallException extends RuntimeException
{
    /**
     * Creates a new exception instance for a test exception.
     *
     * @return self A new exception instance for testing throttle exception handling
     */
    public static function create(): self
    {
        return new self('Test exception');
    }
}
