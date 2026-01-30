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
 * Thrown when accessing a missing property during testing.
 *
 * This exception is used in tests to convert PHP warnings about missing
 * properties into catchable exceptions, allowing verification that property
 * access functions correctly handle non-existent properties.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class MissingPropertyException extends Exception
{
    /**
     * Creates a new exception instance from an error message and code.
     *
     * @param  string $message The error message describing the missing property
     * @param  int    $code    The error code from the PHP warning
     * @return self   A new exception instance with the error details
     */
    public static function fromError(string $message, int $code): self
    {
        return new self($message, $code);
    }
}
