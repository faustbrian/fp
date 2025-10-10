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
 * @author Brian Faust <brian@cline.sh>
 */
final class ZeroStepException extends InvalidArgumentException
{
    public static function create(): self
    {
        return new self('Step cannot be zero');
    }
}
