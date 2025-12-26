<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Cline\fp\Exceptions\InvalidTupleException;

use function array_values;
use function is_array;
use function throw_unless;

/**
 * Transform an array of tuples into separate arrays grouped by position.
 *
 * The inverse operation of `zip()`. Takes an array where each element is itself
 * an array (tuple), and reorganizes the data so that all first elements are
 * grouped together, all second elements are grouped together, and so on.
 * Each inner array can have different lengths; the result will have as many
 * arrays as the longest tuple.
 *
 * ```php
 * $pairs = [[1, 'a'], [2, 'b'], [3, 'c']];
 * $separated = unzip($pairs);
 * // Result: [[1, 2, 3], ['a', 'b', 'c']]
 *
 * $mixed = [[1, 'a', true], [2, 'b'], [3, 'c', false, 'x']];
 * $separated = unzip($mixed);
 * // Result: [[1, 2, 3], ['a', 'b', 'c'], [true, false], ['x']]
 * ```
 *
 * @param array<array<mixed>> $tuples array of arrays where each inner array
 *                                    represents a tuple to be unzipped into
 *                                    separate positional groups
 *
 * @throws InvalidTupleException when any element in the input array is not itself an array
 *
 * @return array<array<mixed>> Array of arrays where each inner array contains
 *                             all values from the same position across all tuples.
 *                             Returns empty array if input is empty.
 */
function unzip(array $tuples): array
{
    if ($tuples === []) {
        return [];
    }

    $result = [];

    foreach ($tuples as $tuple) {
        throw_unless(is_array($tuple), InvalidTupleException::create($tuple));

        foreach ($tuple as $index => $value) {
            $result[$index] ??= [];
            $result[$index][] = $value;
        }
    }

    return array_values($result);
}
