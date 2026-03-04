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
 * Returns a curried function that adds the specified value as the first element
 * of an array. Opposite of append() which adds to the end. When a numeric or string
 * key is provided, it is used for the prepended element. For string keys, the union
 * operator (+) preserves existing keys; for numeric keys or no key, array_merge()
 * reindexes numeric keys sequentially.
 *
 * ```php
 * $addZero = prepend(0);
 * $addZero([1, 2, 3]); // [0, 1, 2, 3]
 *
 * $addHeader = prepend('header', 'title');
 * $addHeader(['a' => 1]); // ['title' => 'header', 'a' => 1]
 *
 * $prependItem = prepend('first', 0);
 * $prependItem(['a', 'b']); // [0 => 'first', 1 => 'a', 2 => 'b']
 * ```
 *
 * @param  mixed           $value the value to prepend to arrays
 * @param  null|int|string $key   Optional key for the prepended value. Integer keys trigger reindexing
 *                                via array_merge, string keys preserve existing keys via union operator.
 * @return Closure         a function accepting an array and returning a new array with the value prepended
 *
 * @api
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
