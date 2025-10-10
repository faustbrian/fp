<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function in_array;
use function is_array;

/**
 * Checks if an iterable contains a specific value.
 *
 * Returns a curried function that searches through an iterable for the
 * given value using strict comparison. Returns true if found, false otherwise.
 * For arrays, uses optimized in_array. For other iterables, manually searches.
 *
 * ```php
 * $hasThree = contains(3);
 * $hasThree([1, 2, 3, 4]); // true
 * $hasThree([1, 2, 4]); // false
 *
 * $hasApple = contains('apple');
 * $hasApple(['orange', 'banana', 'apple']); // true
 * ```
 *
 * @param  mixed                   $needle The value to search for
 * @return Closure(iterable): bool Function accepting iterable and returning bool
 */
function contains(mixed $needle): Closure
{
    return static function (iterable $haystack) use ($needle): bool {
        if (is_array($haystack)) {
            return in_array($needle, $haystack, true);
        }

        foreach ($haystack as $v) {
            if ($v === $needle) {
                return true;
            }
        }

        return false;
    };
}
