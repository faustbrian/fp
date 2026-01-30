<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

/**
 * Returns the input value unchanged.
 *
 * The identity function simply returns whatever value is passed to it.
 * Fundamental in functional programming for use as a default function,
 * in function composition, or as a placeholder in higher-order functions.
 *
 * ```php
 * identity(5); // 5
 * identity('hello'); // 'hello'
 * identity([1, 2, 3]); // [1, 2, 3]
 *
 * // Useful as default mapper
 * $values = map(identity())($array);
 *
 * // Or in composition
 * $transform = compose(identity(), trim(...));
 * ```
 *
 * @param  mixed $value Any value to return unchanged
 * @return mixed The same value that was input
 */
function identity(mixed $value): mixed
{
    return $value;
}
