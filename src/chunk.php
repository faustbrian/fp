<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Cline\fp\Exceptions\InvalidChunkSizeException;
use Closure;

use function throw_if;

/**
 * Splits an iterable into fixed-size chunks, preserving keys.
 *
 * Returns a closure that divides any iterable into an array of smaller arrays,
 * each containing up to the specified number of elements. The last chunk may
 * contain fewer elements if the iterable size is not evenly divisible. Original
 * keys are preserved within each chunk. This is a curried function for use in
 * functional pipelines. Useful for batch processing, pagination, or dividing
 * work into manageable segments.
 *
 * ```php
 * $chunk3 = chunk(3);
 * $chunk3([1, 2, 3, 4, 5, 6, 7]); // [[1, 2, 3], [4, 5, 6], [7]]
 * $chunk3(['a' => 1, 'b' => 2, 'c' => 3]); // [['a' => 1, 'b' => 2, 'c' => 3]]
 *
 * // Batch processing
 * $batchOf100 = chunk(100);
 * foreach ($batchOf100($users) as $batch) {
 *     processUserBatch($batch);
 * }
 *
 * // Pagination display
 * $itemsPerPage = chunk(25);
 * $pages = $itemsPerPage($allItems);
 * ```
 *
 * @param  int     $size Number of elements per chunk. Must be greater than 0.
 *                       Determines the maximum size of each chunk in the result.
 * @return Closure Returns a closure accepting an iterable and returning an array
 *                 of arrays, each containing up to $size elements with original
 *                 keys preserved. The outer array uses numeric keys starting from 0.
 *                 The returned closure throws InvalidChunkSizeException if $size <= 0.
 */
function chunk(int $size): Closure
{
    return static function (iterable $it) use ($size): array {
        throw_if($size <= 0, InvalidChunkSizeException::create($size));

        $result = [];
        $current = [];
        $count = 0;

        foreach ($it as $k => $v) {
            $current[$k] = $v;
            ++$count;

            if ($count === $size) {
                $result[] = $current;
                $current = [];
                $count = 0;
            }
        }

        if ($current !== []) {
            $result[] = $current;
        }

        return $result;
    };
}
