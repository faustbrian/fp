<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\reduce;
use function Cline\fp\reduceWithKeys;
use function describe;
use function expect;
use function it;

describe('reduce', function (): void {
    describe('Happy Path', function (): void {
        it('reduces array to sum', function (): void {
            $result = reduce(0, fn (int $collect, int $x): int => $x + $collect)([1, 2, 3, 4, 5]);
            expect($result)->toBe(15);
        });

        it('reduces with keys including key in calculation', function (): void {
            $result = reduceWithKeys(0, fn (int $collect, int $x, int $k): int => $x + $collect + $k)([1, 2, 3, 4, 5]);
            expect($result)->toBe(25);
        });

        it('reduces iterable to sum', function (): void {
            $gen = function () {
                yield from [1, 2, 3, 4, 5];
            };
            $result = reduce(0, fn (int $collect, int $x): int => $x + $collect)($gen());
            expect($result)->toBe(15);
        });

        it('reduces iterable with keys', function (): void {
            $gen = function () {
                yield from [1, 2, 3, 4, 5];
            };
            $result = reduceWithKeys(0, fn (int $collect, int $x, int $k): int => $x + $collect + $k)($gen());
            expect($result)->toBe(25);
        });
    });

    describe('Edge Cases', function (): void {
        it('reduces empty array to initial value', function (): void {
            $result = reduce(10, fn (int $collect, int $x): int => $x + $collect)([]);
            expect($result)->toBe(10);
        });

        it('reduces single element array', function (): void {
            $result = reduce(0, fn (int $collect, int $x): int => $x + $collect)([5]);
            expect($result)->toBe(5);
        });
    });
});
