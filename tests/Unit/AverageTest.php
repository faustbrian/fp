<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\average;
use function describe;
use function expect;
use function round;
use function test;

describe('average', function (): void {
    describe('Happy Paths', function (): void {
        test('calculates average of integers', function (): void {
            expect(average([1, 2, 3, 4, 5]))->toBe(3);
        });

        test('calculates average of floats', function (): void {
            expect(average([1.5, 2.5, 3.5]))->toBe(2.5);
        });

        test('calculates average of negative numbers', function (): void {
            expect(average([-10, -20, -30]))->toBe(-20);
        });

        test('calculates average with mixed values', function (): void {
            expect(average([10, -5, 5, -10]))->toBe(0);
        });
    });

    describe('Edge Cases', function (): void {
        test('returns null for empty array', function (): void {
            expect(average([]))->toBeNull();
        });

        test('returns value for single element array', function (): void {
            expect(average([42]))->toBe(42);
        });

        test('handles floats with precision', function (): void {
            $result = average([1.1, 2.2, 3.3]);
            expect($result)->toBeFloat();
            expect(round($result, 1))->toBe(2.2);
        });

        test('works with iterator', function (): void {
            $generator = function () {
                yield 1;

                yield 2;

                yield 3;
            };
            expect(average($generator()))->toBe(2);
        });
    });
});
