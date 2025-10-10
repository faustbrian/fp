<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\keyedMap;
use function describe;
use function expect;
use function it;

describe('keyedMap', function (): void {
    describe('Happy Path', function (): void {
        it('maps values using key and value', function (): void {
            $result = keyedMap(static fn ($k, $v) => $k + $v)([1 => 1, 2 => 2, 3 => 3]);
            expect($result)->toBe([0 => 2, 1 => 4, 2 => 6]);
        });

        it('maps both keys and values with callbacks', function (): void {
            $values = static fn ($k, $v): int|float => $k * $v;
            $keys = static fn ($k, $v): float|int|array => $k + $v;
            $result = keyedMap($values, $keys)([1 => 1, 2 => 2, 3 => 3]);
            expect($result)->toBe([2 => 1, 4 => 4, 6 => 9]);
        });
    });
});
