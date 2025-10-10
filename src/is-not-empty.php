<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\isEmpty;

/**
 * Checks if a value is not empty, providing the inverse of isEmpty().
 *
 * This function provides comprehensive non-empty checking for various PHP types
 * by negating the result of isEmpty(). It handles arrays, Countable objects,
 * strings, Traversable objects, and other types with type-appropriate logic.
 *
 * ```php
 * isNotEmpty([]); // false
 * isNotEmpty([1, 2, 3]); // true
 * isNotEmpty(''); // false
 * isNotEmpty('hello'); // true
 * isNotEmpty(new ArrayObject([1])); // true
 * ```
 *
 * @param  mixed $value The value to check for non-emptiness. Accepts any type, with special
 *                      handling for arrays, Countable objects, strings, and Traversable objects.
 * @return bool  returns true if the value contains data according to type-specific rules,
 *               false if it is considered empty
 *
 * @see isEmpty() For detailed emptiness checking rules by type.
 */
function isNotEmpty(mixed $value): bool
{
    return !isEmpty($value);
}
