<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\product;
use function describe;
use function expect;
use function test;

describe('product', function (): void {
    describe('Happy Paths', function (): void {
        test('multiplies integers in an array', function (): void {
            expect(product([2, 3, 4]))->toBe(24);
        });

        test('multiplies float values in an array', function (): void {
            expect(product([1.5, 2.0, 3.0]))->toBe(9.0);
        });

        test('multiplies negative numbers', function (): void {
            expect(product([-2, -3, -4]))->toBe(-24);
        });

        test('multiplies mixed positive and negative numbers', function (): void {
            expect(product([2, -3, 4]))->toBe(-24);
        });
    });

    describe('Edge Cases', function (): void {
        test('returns 1 for empty array', function (): void {
            expect(product([]))->toBe(1);
        });

        test('returns value for single element array', function (): void {
            expect(product([42]))->toBe(42);
        });

        test('returns 0 when array contains zero', function (): void {
            expect(product([1, 2, 0, 4]))->toBe(0);
        });

        test('works with iterator', function (): void {
            $generator = function () {
                yield 2;

                yield 3;

                yield 4;
            };
            expect(product($generator()))->toBe(24);
        });
    });
});
