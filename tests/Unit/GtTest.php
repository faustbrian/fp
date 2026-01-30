<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\afilter;
use function Cline\fp\andPred;
use function Cline\fp\gt;
use function Cline\fp\lt;
use function Cline\fp\not;
use function describe;
use function expect;
use function it;

describe('gt', function (): void {
    describe('Happy Path', function (): void {
        it('returns true when value is greater', function (): void {
            $greaterThan10 = gt(10);

            expect($greaterThan10(15))->toBeTrue();
            expect($greaterThan10(100))->toBeTrue();
        });

        it('returns false when value is less than or equal', function (): void {
            $greaterThan10 = gt(10);

            expect($greaterThan10(10))->toBeFalse();
            expect($greaterThan10(5))->toBeFalse();
        });

        it('works with float values', function (): void {
            $greaterThan5Point5 = gt(5.5);

            expect($greaterThan5Point5(5.6))->toBeTrue();
            expect($greaterThan5Point5(10.0))->toBeTrue();
            expect($greaterThan5Point5(5.5))->toBeFalse();
            expect($greaterThan5Point5(5.4))->toBeFalse();
        });

        it('filters array of values', function (): void {
            $numbers = [1, 5, 10, 15, 20];
            $filtered = afilter(gt(10))($numbers);

            expect($filtered)->toBe([3 => 15, 4 => 20]);
        });
    });

    describe('Edge Cases', function (): void {
        it('handles negative numbers', function (): void {
            $greaterThanMinus5 = gt(-5);

            expect($greaterThanMinus5(-4))->toBeTrue();
            expect($greaterThanMinus5(0))->toBeTrue();
            expect($greaterThanMinus5(-5))->toBeFalse();
            expect($greaterThanMinus5(-10))->toBeFalse();
        });

        it('handles zero boundary', function (): void {
            $greaterThanZero = gt(0);

            expect($greaterThanZero(1))->toBeTrue();
            expect($greaterThanZero(0))->toBeFalse();
            expect($greaterThanZero(-1))->toBeFalse();
        });

        it('works with not() to invert predicate', function (): void {
            $notGreaterThan10 = not(gt(10));

            expect($notGreaterThan10(5))->toBeTrue();
            expect($notGreaterThan10(10))->toBeTrue();
            expect($notGreaterThan10(15))->toBeFalse();
        });

        it('combines with andPred for complex logic', function (): void {
            $between10And20 = andPred(gt(10), lt(20));

            expect($between10And20(15))->toBeTrue();
            expect($between10And20(10))->toBeFalse();
            expect($between10And20(20))->toBeFalse();
            expect($between10And20(5))->toBeFalse();
            expect($between10And20(25))->toBeFalse();
        });
    });
});
