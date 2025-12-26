<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function array_reverse;
use function is_array;
use function iterator_to_array;

/**
 * Right fold operation - reduces iterable from right to left.
 *
 * Reduces an iterable to a single value by applying a reducer function
 * from right to left. This is the opposite direction of reduce/foldl.
 * Useful when the order of operations matters and you need to process
 * from the end to the beginning.
 *
 * ```php
 * $buildList = foldr([], fn($acc, $x) => [$x, ...$acc]);
 * $buildList([1, 2, 3]); // [1, 2, 3] (processes 3->2->1, prepending each)
 *
 * $concat = foldr('', fn($acc, $char) => $acc . $char);
 * $concat(['a', 'b', 'c']); // 'cba' (processes c->b->a)
 * ```
 *
 * @see reduce() For left-to-right reduction
 * @see foldl() For explicit left fold
 * @param  mixed    $init Initial accumulator value
 * @param  callable $c    Reducer function combining accumulator with each element
 * @return Closure  Function accepting iterable and returning accumulated value
 */
function foldr(mixed $init, callable $c): Closure
{
    return static function (iterable $it) use ($init, $c): mixed {
        $array = is_array($it) ? $it : iterator_to_array($it);
        $reversed = array_reverse($array, true);

        foreach ($reversed as $v) {
            $init = $c($init, $v);
        }

        return $init;
    };
}
