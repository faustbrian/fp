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
 * Thrown when a side effect operation fails during testing.
 *
 * This exception is used in tests to simulate failures in side effect callbacks,
 * ensuring that exception propagation is handled correctly by functional wrappers.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class SideEffectException extends RuntimeException
{
    /**
     * Creates a new exception instance for a side effect error.
     *
     * @return self A new exception instance indicating a side effect failure
     */
    public static function create(): self
    {
        return new self('Side effect error');
    }
}
