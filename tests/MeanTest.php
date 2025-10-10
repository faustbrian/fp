<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\average;
use function Cline\fp\mean;
use function describe;
use function expect;
use function test;

describe('mean', function (): void {
    describe('Happy Paths', function (): void {
        test('calculates mean of integers', function (): void {
            expect(mean([2, 4, 6, 8, 10]))->toBe(6);
        });

        test('is an alias for average', function (): void {
            $values = [1, 2, 3, 4, 5];
            expect(mean($values))->toBe(average($values));
        });
    });

    describe('Edge Cases', function (): void {
        test('returns null for empty array', function (): void {
            expect(mean([]))->toBeNull();
        });

        test('returns value for single element array', function (): void {
            expect(mean([42]))->toBe(42);
        });
    });
});
