<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\partial;
use function describe;
use function expect;
use function it;

describe('partial', function (): void {
    describe('Happy Path', function (): void {
        it('partially applies 1 argument', function (): void {
            $add = static fn (int $a, int $b): int => $a + $b;
            $addFive = partial($add, 5);

            expect($addFive(10))->toBe(15);
        });

        it('partially applies multiple arguments', function (): void {
            $sum = static fn (int $a, int $b, int $c): int => $a + $b + $c;
            $partialSum = partial($sum, 5, 10);

            expect($partialSum(15))->toBe(30);
        });

        it('partially applies then calls with remaining args', function (): void {
            $multiply = static fn (int $a, int $b, int $c): int => $a * $b * $c;
            $partialMultiply = partial($multiply, 2);
            $result = $partialMultiply(3, 4);

            expect($result)->toBe(24);
        });
    });

    describe('Edge Cases', function (): void {
        it('partially applies all arguments', function (): void {
            $add = static fn (int $a, int $b): int => $a + $b;
            $partial = partial($add, 5, 10);

            expect($partial())->toBe(15);
        });

        it('partially applies no arguments', function (): void {
            $add = static fn (int $a, int $b): int => $a + $b;
            $partial = partial($add);

            expect($partial(5, 10))->toBe(15);
        });
    });
});
