<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\afilter;
use function Cline\fp\gt;
use function Cline\fp\lt;
use function Cline\fp\orPred;
use function describe;
use function expect;
use function it;

describe('lt', function (): void {
    describe('Happy Path', function (): void {
        it('returns true when value is less than', function (): void {
            $lessThan10 = lt(10);

            expect($lessThan10(5))->toBeTrue();
            expect($lessThan10(0))->toBeTrue();
        });

        it('returns false when value is greater than or equal', function (): void {
            $lessThan10 = lt(10);

            expect($lessThan10(10))->toBeFalse();
            expect($lessThan10(15))->toBeFalse();
        });

        it('works with float values', function (): void {
            $lessThan5Point5 = lt(5.5);

            expect($lessThan5Point5(5.4))->toBeTrue();
            expect($lessThan5Point5(0.0))->toBeTrue();
            expect($lessThan5Point5(5.5))->toBeFalse();
            expect($lessThan5Point5(5.6))->toBeFalse();
        });

        it('filters array of values', function (): void {
            $numbers = [1, 5, 10, 15, 20];
            $filtered = afilter(lt(10))($numbers);

            expect($filtered)->toBe([0 => 1, 1 => 5]);
        });
    });

    describe('Edge Cases', function (): void {
        it('handles negative numbers', function (): void {
            $lessThanMinus5 = lt(-5);

            expect($lessThanMinus5(-10))->toBeTrue();
            expect($lessThanMinus5(-6))->toBeTrue();
            expect($lessThanMinus5(-5))->toBeFalse();
            expect($lessThanMinus5(0))->toBeFalse();
        });

        it('handles zero boundary', function (): void {
            $lessThanZero = lt(0);

            expect($lessThanZero(-1))->toBeTrue();
            expect($lessThanZero(0))->toBeFalse();
            expect($lessThanZero(1))->toBeFalse();
        });

        it('combines with orPred for complex logic', function (): void {
            $outsideRange = orPred(lt(10), gt(20));

            expect($outsideRange(5))->toBeTrue();
            expect($outsideRange(25))->toBeTrue();
            expect($outsideRange(15))->toBeFalse();
        });
    });
});
