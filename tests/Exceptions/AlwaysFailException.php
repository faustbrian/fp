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
 * Thrown to simulate an operation that always fails.
 *
 * This exception is used in tests to simulate operations that will never
 * succeed, allowing verification that retry mechanisms correctly exhaust
 * all attempts and propagate the final exception.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class AlwaysFailException extends Exception
{
    /**
     * Creates a new exception instance for an always-failing operation.
     *
     * @return self A new exception instance indicating an operation that always fails
     */
    public static function create(): self
    {
        return new self('Always fail');
    }
}
