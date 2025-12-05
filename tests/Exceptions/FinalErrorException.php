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
 * Thrown to simulate a final error in a retry sequence during testing.
 *
 * This exception is used in tests to simulate the final exception thrown
 * after all retry attempts have been exhausted, allowing verification
 * that the retry mechanism correctly propagates the last error.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class FinalErrorException extends RuntimeException
{
    /**
     * Creates a new exception instance for the final error.
     *
     * @return self A new exception instance indicating the final error
     */
    public static function create(): self
    {
        return new self('Final error');
    }
}
