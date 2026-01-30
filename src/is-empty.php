<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Countable;
use Traversable;

use function count;
use function is_array;
use function is_string;

/**
 * Checks if a value is empty, handling multiple data types appropriately.
 *
 * This function provides comprehensive empty checking for various PHP types:
 * - Arrays: uses PHP's native empty() check
 * - Countable objects: checks if count equals zero
 * - Strings: checks if the string is exactly an empty string
 * - Traversable objects: attempts iteration to detect if any elements exist
 * - Other types: falls back to PHP's native empty() check
 *
 * ```php
 * isEmpty([]); // true
 * isEmpty([1, 2, 3]); // false
 * isEmpty(''); // true
 * isEmpty('hello'); // false
 * isEmpty(new ArrayObject([])); // true
 * isEmpty(new ArrayIterator([1, 2])); // false
 * ```
 *
 * @param  mixed $value The value to check for emptiness. Accepts any type, with special
 *                      handling for arrays, Countable objects, strings, and Traversable objects.
 * @return bool  returns true if the value is considered empty according to type-specific
 *               rules, false otherwise
 */
function isEmpty(mixed $value): bool
{
    if (is_array($value)) {
        return $value === [];
    }

    if ($value instanceof Countable) {
        return count($value) === 0;
    }

    if (is_string($value)) {
        return $value === '';
    }

    if ($value instanceof Traversable) {
        foreach ($value as $_) {
            return false;
        }

        return true;
    }

    return empty($value);
}
