<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\afilter;
use function Cline\fp\gte;
use function Cline\fp\pipe;
use function describe;
use function expect;
use function it;

describe('gte', function (): void {
    describe('Happy Path', function (): void {
        it('returns true when value is greater or equal', function (): void {
            $greaterThanOrEqual10 = gte(10);

            expect($greaterThanOrEqual10(10))->toBeTrue();
            expect($greaterThanOrEqual10(15))->toBeTrue();
            expect($greaterThanOrEqual10(100))->toBeTrue();
        });

        it('returns false when value is less than', function (): void {
            $greaterThanOrEqual10 = gte(10);

            expect($greaterThanOrEqual10(9))->toBeFalse();
            expect($greaterThanOrEqual10(5))->toBeFalse();
        });

        it('works with float values', function (): void {
            $greaterThanOrEqual5Point5 = gte(5.5);

            expect($greaterThanOrEqual5Point5(5.5))->toBeTrue();
            expect($greaterThanOrEqual5Point5(5.6))->toBeTrue();
            expect($greaterThanOrEqual5Point5(5.4))->toBeFalse();
        });

        it('filters array of values', function (): void {
            $numbers = [1, 5, 10, 15, 20];
            $filtered = afilter(gte(10))($numbers);

            expect($filtered)->toBe([2 => 10, 3 => 15, 4 => 20]);
        });
    });

    describe('Edge Cases', function (): void {
        it('handles negative numbers', function (): void {
            $greaterThanOrEqualMinus5 = gte(-5);

            expect($greaterThanOrEqualMinus5(-5))->toBeTrue();
            expect($greaterThanOrEqualMinus5(-4))->toBeTrue();
            expect($greaterThanOrEqualMinus5(0))->toBeTrue();
            expect($greaterThanOrEqualMinus5(-6))->toBeFalse();
        });

        it('handles zero boundary', function (): void {
            $greaterThanOrEqualZero = gte(0);

            expect($greaterThanOrEqualZero(0))->toBeTrue();
            expect($greaterThanOrEqualZero(1))->toBeTrue();
            expect($greaterThanOrEqualZero(-1))->toBeFalse();
        });

        it('works in pipe to filter positive numbers', function (): void {
            $numbers = [-10, -5, 0, 5, 10];
            $result = pipe(
                $numbers,
                afilter(gte(0)),
            );

            expect($result)->toBe([2 => 0, 3 => 5, 4 => 10]);
        });
    });
});
