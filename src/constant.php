<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

/**
 * Creates a function that always returns the same value.
 *
 * Returns a closure that ignores its arguments and always returns the
 * value provided to constant(). Useful for providing default values,
 * creating placeholder functions, or in functional pipelines where a
 * constant value is needed regardless of input.
 *
 * ```php
 * $alwaysTrue = constant(true);
 * $alwaysTrue(); // true
 * $alwaysTrue(1, 2, 3); // true (ignores arguments)
 *
 * $defaultUser = constant(['id' => 0, 'name' => 'Guest']);
 * $defaultUser(); // ['id' => 0, 'name' => 'Guest']
 *
 * // Useful in pipelines
 * $result = pipe($data, when($isInvalid, constant(null)));
 * ```
 *
 * @param  mixed   $value The value to always return
 * @return Closure Function that ignores arguments and returns the constant value
 */
function constant(mixed $value): Closure
{
    return static fn (...$args): mixed => $value;
}
