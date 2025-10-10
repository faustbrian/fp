<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function array_merge;
use function is_int;

/**
 * Creates a function that prepends a value to the beginning of an array.
 *
 * Returns a curried function that adds the specified value as the first
 * element of an array. Opposite of append() which adds to the end.
 * Numeric keys are reindexed. If a key is provided, uses it for the
 * prepended value.
 *
 * ```php
 * $addZero = prepend(0);
 * $addZero([1, 2, 3]); // [0, 1, 2, 3]
 *
 * $addHeader = prepend('header', 'title');
 * $addHeader(['a' => 1]); // ['title' => 'header', 'a' => 1]
 * ```
 *
 * @param  mixed      $value The value to prepend to arrays
 * @param  null|mixed $key   Optional key for the prepended value
 * @return Closure    Function accepting an array and returning array with value prepended
 */
function prepend(mixed $value, mixed $key = null): Closure
{
    return static function (array $arr) use ($value, $key): array {
        if (null === $key) {
            return array_merge([$value], $arr);
        }

        if (is_int($key)) {
            return array_merge([$key => $value], $arr);
        }

        /** @var int|string $key */
        return [$key => $value] + $arr;
    };
}
