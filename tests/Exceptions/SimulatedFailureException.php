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
 * Thrown to simulate a generic failure during testing.
 *
 * This exception is used in tests to simulate generic failures that don't
 * need specific context, allowing verification of exception handling paths.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class SimulatedFailureException extends Exception
{
    /**
     * Creates a new exception instance for a simulated failure.
     *
     * @return self A new exception instance with a generic failure message
     */
    public static function create(): self
    {
        return new self('Failed');
    }
}
