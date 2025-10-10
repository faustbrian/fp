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
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidTupleException extends InvalidArgumentException
{
    public static function create(mixed $value): self
    {
        $type = get_debug_type($value);

        return new self('Each element must be an array, got '.$type);
    }
}
