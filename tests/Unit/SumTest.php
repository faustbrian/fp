<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\sum;
use function describe;
use function expect;
use function test;

describe('sum', function (): void {
    describe('Happy Paths', function (): void {
        test('sums integers in an array', function (): void {
            expect(sum([1, 2, 3, 4, 5]))->toBe(15);
        });

        test('sums float values in an array', function (): void {
            expect(sum([1.5, 2.5, 3.0]))->toBe(7.0);
        });

        test('sums negative numbers', function (): void {
            expect(sum([-1, -2, -3]))->toBe(-6);
        });

        test('sums mixed positive and negative numbers', function (): void {
            expect(sum([10, -5, 3, -2]))->toBe(6);
        });
    });

    describe('Edge Cases', function (): void {
        test('returns 0 for empty array', function (): void {
            expect(sum([]))->toBe(0);
        });

        test('returns value for single element array', function (): void {
            expect(sum([42]))->toBe(42);
        });

        test('handles zero in array', function (): void {
            expect(sum([1, 0, 2, 0, 3]))->toBe(6);
        });

        test('works with iterator', function (): void {
            $generator = function () {
                yield 1;

                yield 2;

                yield 3;
            };
            expect(sum($generator()))->toBe(6);
        });
    });
});
