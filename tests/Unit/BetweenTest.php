<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\afilter;
use function Cline\fp\between;
use function Cline\fp\not;
use function Cline\fp\pipe;
use function describe;
use function expect;
use function it;

describe('between', function (): void {
    describe('Happy Path', function (): void {
        it('returns true for value within range', function (): void {
            $between5And10 = between(5, 10);

            expect($between5And10(7))->toBeTrue();
            expect($between5And10(8))->toBeTrue();
        });

        it('returns true for values at boundaries (inclusive)', function (): void {
            $between5And10 = between(5, 10);

            expect($between5And10(5))->toBeTrue();
            expect($between5And10(10))->toBeTrue();
        });

        it('returns false for values outside range', function (): void {
            $between5And10 = between(5, 10);

            expect($between5And10(4))->toBeFalse();
            expect($between5And10(11))->toBeFalse();
            expect($between5And10(0))->toBeFalse();
            expect($between5And10(100))->toBeFalse();
        });

        it('works with float ranges', function (): void {
            $between1Point5And3Point5 = between(1.5, 3.5);

            expect($between1Point5And3Point5(2.0))->toBeTrue();
            expect($between1Point5And3Point5(1.5))->toBeTrue();
            expect($between1Point5And3Point5(3.5))->toBeTrue();
            expect($between1Point5And3Point5(1.4))->toBeFalse();
            expect($between1Point5And3Point5(3.6))->toBeFalse();
        });

        it('filters array of values', function (): void {
            $numbers = [1, 5, 10, 15, 20, 25];
            $filtered = afilter(between(10, 20))($numbers);

            expect($filtered)->toBe([2 => 10, 3 => 15, 4 => 20]);
        });
    });

    describe('Edge Cases', function (): void {
        it('handles negative ranges', function (): void {
            $betweenMinus10AndMinus5 = between(-10, -5);

            expect($betweenMinus10AndMinus5(-7))->toBeTrue();
            expect($betweenMinus10AndMinus5(-10))->toBeTrue();
            expect($betweenMinus10AndMinus5(-5))->toBeTrue();
            expect($betweenMinus10AndMinus5(-11))->toBeFalse();
            expect($betweenMinus10AndMinus5(-4))->toBeFalse();
            expect($betweenMinus10AndMinus5(0))->toBeFalse();
        });

        it('handles min equals max (single value range)', function (): void {
            $exactlyFive = between(5, 5);

            expect($exactlyFive(5))->toBeTrue();
            expect($exactlyFive(4))->toBeFalse();
            expect($exactlyFive(6))->toBeFalse();
        });

        it('works in pipe with filtering', function (): void {
            $numbers = [-5, 0, 5, 10, 15, 20, 25];
            $result = pipe(
                $numbers,
                afilter(between(5, 15)),
            );

            expect($result)->toBe([2 => 5, 3 => 10, 4 => 15]);
        });

        it('combines with not() to check outside range', function (): void {
            $outsideRange = not(between(10, 20));

            expect($outsideRange(5))->toBeTrue();
            expect($outsideRange(25))->toBeTrue();
            expect($outsideRange(15))->toBeFalse();
            expect($outsideRange(10))->toBeFalse();
            expect($outsideRange(20))->toBeFalse();
        });
    });
});
