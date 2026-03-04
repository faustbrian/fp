<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\minValue;
use function describe;
use function expect;
use function test;

describe('minValue', function (): void {
    describe('Happy Paths', function (): void {
        test('finds minimum value in integer array', function (): void {
            expect(minValue([5, 2, 8, 1, 9]))->toBe(1);
        });

        test('finds minimum value in float array', function (): void {
            expect(minValue([3.5, 1.2, 5.7, 2.1, 4.8]))->toBe(1.2);
        });

        test('finds minimum in negative numbers', function (): void {
            expect(minValue([-5, -10, -3, -20]))->toBe(-20);
        });

        test('finds minimum in mixed int and float', function (): void {
            expect(minValue([5, 2.3, 8, 1.1, 9]))->toBe(1.1);
        });
    });

    describe('Edge Cases', function (): void {
        test('returns null for empty array', function (): void {
            expect(minValue([]))->toBeNull();
        });

        test('returns value for single element array', function (): void {
            expect(minValue([42]))->toBe(42);
        });

        test('handles zero as minimum', function (): void {
            expect(minValue([5, 10, 0, 3]))->toBe(0);
        });

        test('works with iterator', function (): void {
            $generator = function () {
                yield 5;

                yield 1;

                yield 3;
            };
            expect(minValue($generator()))->toBe(1);
        });
    });
});
