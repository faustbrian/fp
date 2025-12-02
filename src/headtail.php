<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;
use Iterator;

use function Cline\fp\reduce;
use function Cline\fp\tail;
use function current;
use function is_array;

/**
 * Reduces a collection using separate reducers for the first element and remaining elements.
 *
 * Applies different reduction logic to the head versus tail of a collection, which is
 * essential for fencepost problems where the first element requires special handling
 * (e.g., building comma-separated lists, handling initialization differently from accumulation).
 * Works with both arrays and Iterator instances, handling each type appropriately.
 *
 * ```php
 * // Building a comma-separated list with proper formatting
 * $items = ['apple', 'banana', 'cherry'];
 *
 * $buildList = headtail(
 *     '',
 *     fn($acc, $item) => $item,              // First item: no comma
 *     fn($acc, $item) => "$acc, $item"       // Rest: prepend comma
 * );
 *
 * $result = $buildList($items); // "apple, banana, cherry"
 * ```
 *
 * @param  mixed                          $init  The initial accumulator value passed to the first reducer
 * @param  callable                       $first The reducer function applied only to the first element.
 *                                               Receives ($accumulator, $firstElement) and returns the new accumulator.
 * @param  callable                       $rest  The reducer function applied to all remaining elements after the first.
 *                                               Receives ($accumulator, $element) and returns the new accumulator.
 * @return Closure(array|Iterator): mixed Returns a function accepting a collection that
 *                                        produces the final reduced value, or returns the
 *                                        initial value if the collection is empty
 */
function headtail(mixed $init, callable $first, callable $rest): Closure
{
    // \IteratorAggregate is impossible in practice, so `iterable` is too wide.
    return static function (array|Iterator $it) use ($init, $first, $rest): mixed {
        $head = is_array($it) ? current($it) : $it->current();

        if (!$head) {
            return $init;
        }

        $init = $first($init, $head);

        if (is_array($it)) {
            return reduce($init, $rest)(tail($it));
        }

        // Because the iterator has already been started, we cannot use the
        // foreach() loop in reduce().  Instead we have to do it the manual
        // way here.  Blech.
        $it->next();

        while ($it->valid()) {
            $init = $rest($init, $it->current());
            $it->next();
        }

        return $init;
    };
}
