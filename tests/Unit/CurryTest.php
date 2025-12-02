<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\curry;
use function describe;
use function expect;
use function it;

describe('curry', function (): void {
    describe('Happy Path', function (): void {
        it('curries function with 2 args, called with 1 then 1', function (): void {
            $add = static fn (int $a, int $b): int => $a + $b;
            $curried = curry($add);
            $addFive = $curried(5);

            expect($addFive(10))->toBe(15);
        });

        it('curries function with 3 args with progressive application', function (): void {
            $sum = static fn (int $a, int $b, int $c): int => $a + $b + $c;
            $curried = curry($sum);
            $step1 = $curried(5);
            $step2 = $step1(10);

            expect($step2(15))->toBe(30);
        });

        it('curries function called with all args at once', function (): void {
            $add = static fn (int $a, int $b): int => $a + $b;
            $curried = curry($add);

            expect($curried(5, 10))->toBe(15);
        });

        it('curries with explicit arity', function (): void {
            $add = static fn (int $a, int $b, int $c = 0): int => $a + $b + $c;
            $curried = curry($add, 3);
            $step1 = $curried(5);
            $step2 = $step1(10);

            expect($step2(15))->toBe(30);
        });
    });

    describe('Sad Paths', function (): void {
        it('curries function with no required parameters', function (): void {
            $noArgs = static fn (int $a = 5, int $b = 10): int => $a + $b;
            $curried = curry($noArgs);

            expect($curried())->toBe(15);
        });
    });

    describe('Edge Cases', function (): void {
        it('handles nested currying application', function (): void {
            $add = static fn (int $a, int $b): int => $a + $b;
            $curried = curry($add);
            $doubleCurried = curry($curried);

            expect($doubleCurried(5)(10))->toBe(15);
        });

        it('applies more arguments than arity', function (): void {
            $add = static fn (int $a, int $b): int => $a + $b;
            $curried = curry($add);

            expect($curried(5, 10, 999))->toBe(15);
        });
    });
});
