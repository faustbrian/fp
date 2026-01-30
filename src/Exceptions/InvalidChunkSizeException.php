<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp\Exceptions;

use InvalidArgumentException;

/**
 * Thrown when an invalid chunk size is provided to a chunking operation.
 *
 * This exception indicates that a chunk size parameter violates the required
 * constraint of being a positive integer. Chunk operations require a size
 * greater than zero to produce meaningful results.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidChunkSizeException extends InvalidArgumentException implements FpException
{
    /**
     * Creates a new exception instance for invalid chunk size.
     *
     * @param  int  $size The invalid chunk size that was provided. Typically
     *                    a non-positive integer (zero or negative) that cannot
     *                    be used for chunking operations.
     * @return self a new exception instance with a descriptive error message
     *              indicating the invalid size value that was provided
     */
    public static function create(int $size): self
    {
        return new self('Chunk size must be greater than 0, got '.$size);
    }
}
