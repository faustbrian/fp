<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\maxValue;
use function describe;
use function expect;
use function test;

describe('maxValue', function (): void {
    describe('Happy Paths', function (): void {
        test('finds maximum value in integer array', function (): void {
            expect(maxValue([1, 5, 3, 9, 2]))->toBe(9);
        });

        test('finds maximum value in float array', function (): void {
            expect(maxValue([1.5, 3.7, 2.1, 5.9, 4.2]))->toBe(5.9);
        });

        test('finds maximum in negative numbers', function (): void {
            expect(maxValue([-10, -5, -20, -3]))->toBe(-3);
        });

        test('finds maximum in mixed int and float', function (): void {
            expect(maxValue([1, 2.5, 3, 4.7, 5]))->toBe(5);
        });
    });

    describe('Edge Cases', function (): void {
        test('returns null for empty array', function (): void {
            expect(maxValue([]))->toBeNull();
        });

        test('returns value for single element array', function (): void {
            expect(maxValue([42]))->toBe(42);
        });

        test('handles zero as maximum', function (): void {
            expect(maxValue([-5, -10, 0, -3]))->toBe(0);
        });

        test('works with iterator', function (): void {
            $generator = function () {
                yield 1;

                yield 5;

                yield 3;
            };
            expect(maxValue($generator()))->toBe(5);
        });
    });
});
