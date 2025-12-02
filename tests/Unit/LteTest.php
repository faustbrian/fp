<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\afilter;
use function Cline\fp\lte;
use function Cline\fp\pipe;
use function describe;
use function expect;
use function it;

describe('lte', function (): void {
    describe('Happy Path', function (): void {
        it('returns true when value is less than or equal', function (): void {
            $lessThanOrEqual10 = lte(10);

            expect($lessThanOrEqual10(10))->toBeTrue();
            expect($lessThanOrEqual10(5))->toBeTrue();
            expect($lessThanOrEqual10(0))->toBeTrue();
        });

        it('returns false when value is greater than', function (): void {
            $lessThanOrEqual10 = lte(10);

            expect($lessThanOrEqual10(11))->toBeFalse();
            expect($lessThanOrEqual10(15))->toBeFalse();
        });

        it('works with float values', function (): void {
            $lessThanOrEqual5Point5 = lte(5.5);

            expect($lessThanOrEqual5Point5(5.5))->toBeTrue();
            expect($lessThanOrEqual5Point5(5.4))->toBeTrue();
            expect($lessThanOrEqual5Point5(5.6))->toBeFalse();
        });

        it('filters array of values', function (): void {
            $numbers = [1, 5, 10, 15, 20];
            $filtered = afilter(lte(10))($numbers);

            expect($filtered)->toBe([0 => 1, 1 => 5, 2 => 10]);
        });
    });

    describe('Edge Cases', function (): void {
        it('handles negative numbers', function (): void {
            $lessThanOrEqualMinus5 = lte(-5);

            expect($lessThanOrEqualMinus5(-10))->toBeTrue();
            expect($lessThanOrEqualMinus5(-5))->toBeTrue();
            expect($lessThanOrEqualMinus5(-4))->toBeFalse();
            expect($lessThanOrEqualMinus5(0))->toBeFalse();
        });

        it('handles zero boundary', function (): void {
            $lessThanOrEqualZero = lte(0);

            expect($lessThanOrEqualZero(-1))->toBeTrue();
            expect($lessThanOrEqualZero(0))->toBeTrue();
            expect($lessThanOrEqualZero(1))->toBeFalse();
        });

        it('works in pipe to filter non-positive numbers', function (): void {
            $numbers = [-10, -5, 0, 5, 10];
            $result = pipe(
                $numbers,
                afilter(lte(0)),
            );

            expect($result)->toBe([0 => -10, 1 => -5, 2 => 0]);
        });
    });
});
