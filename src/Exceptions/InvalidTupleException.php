<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp\Exceptions;

use InvalidArgumentException;

use function get_debug_type;

/**
 * Thrown when a non-array value is encountered in a tuple operation.
 *
 * This exception indicates that an element in a collection expected to contain
 * array tuples contains a non-array value. Tuple operations require each element
 * to be an array structure for proper destructuring and processing.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidTupleException extends InvalidArgumentException implements FpException
{
    /**
     * Creates a new exception instance for invalid tuple type.
     *
     * @param  mixed $value The invalid value that was encountered where an array
     *                      tuple was expected. Can be any PHP value type (string,
     *                      integer, object, etc.) that is not an array.
     * @return self  a new exception instance with a descriptive error message
     *               indicating the actual type of the invalid value using PHP's
     *               debug type representation for clarity
     */
    public static function create(mixed $value): self
    {
        $type = get_debug_type($value);

        return new self('Each element must be an array, got '.$type);
    }
}
