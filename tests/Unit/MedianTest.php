<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\median;
use function describe;
use function expect;
use function test;

describe('median', function (): void {
    describe('Happy Paths', function (): void {
        test('calculates median of odd count array', function (): void {
            expect(median([1, 3, 5, 7, 9]))->toBe(5);
        });

        test('calculates median of even count array', function (): void {
            expect(median([1, 2, 3, 4]))->toBe(2.5);
        });

        test('calculates median with unsorted values', function (): void {
            expect(median([9, 1, 5, 3, 7]))->toBe(5);
        });

        test('calculates median of floats', function (): void {
            expect(median([1.5, 2.5, 3.5]))->toBe(2.5);
        });
    });

    describe('Edge Cases', function (): void {
        test('returns null for empty array', function (): void {
            expect(median([]))->toBeNull();
        });

        test('returns value for single element array', function (): void {
            expect(median([42]))->toBe(42);
        });

        test('handles duplicate values', function (): void {
            expect(median([5, 5, 5, 5, 5]))->toBe(5);
        });

        test('works with iterator', function (): void {
            $generator = function () {
                yield 3;

                yield 1;

                yield 2;
            };
            expect(median($generator()))->toBe(2);
        });

        test('handles two elements', function (): void {
            expect(median([1, 3]))->toBe(2);
        });
    });
});
