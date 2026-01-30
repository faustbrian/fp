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
 * Thrown when a debounced function call fails during testing.
 *
 * This exception is used in tests to simulate failures within debounced
 * functions, allowing verification that exception handling works correctly
 * with debounce decorators.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class DebouncedCallException extends RuntimeException
{
    /**
     * Creates a new exception instance for a test exception.
     *
     * @return self A new exception instance for testing debounce exception handling
     */
    public static function create(): self
    {
        return new self('Test exception');
    }
}
