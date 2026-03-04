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
 * Groups iterable elements by keys generated from a callback function.
 *
 * Creates a curried function that organizes elements into groups based on the key
 * returned by the keyMaker callback. Original keys are preserved within each group,
 * making this ideal for maintaining associative relationships while organizing data.
 *
 * ```php
 * $products = [
 *     ['name' => 'Laptop', 'category' => 'Electronics', 'price' => 999],
 *     ['name' => 'Mouse', 'category' => 'Electronics', 'price' => 29],
 *     ['name' => 'Desk', 'category' => 'Furniture', 'price' => 299],
 * ];
 *
 * $groupByCategory = groupBy(fn($p) => $p['category']);
 * $grouped = $groupByCategory($products);
 * // [
 * //   'Electronics' => [['name' => 'Laptop', ...], ['name' => 'Mouse', ...]],
 * //   'Furniture' => [['name' => 'Desk', ...]]
 * // ]
 * ```
 *
 * @param  callable                                      $keyMaker Callback function receiving each value and returning the group key
 * @return Closure(iterable): array<mixed, array<mixed>> Returns a function accepting an iterable
 *                                                       that produces an array of grouped elements
 */
function groupBy(callable $keyMaker): Closure
{
    return static function (iterable $it) use ($keyMaker): array {
        $groups = [];

        foreach ($it as $k => $v) {
            $groupKey = $keyMaker($v);
            $groups[$groupKey] ??= [];
            $groups[$groupKey][$k] = $v;
        }

        return $groups;
    };
}
