<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use InvalidArgumentException;

use function abs;
use function Cline\fp\range;
use function describe;
use function expect;
use function test;

describe('range', function (): void {
    describe('Happy Paths', function (): void {
        test('generates ascending range from 1 to 10', function (): void {
            // Arrange & Act
            $result = range(1, 10);

            // Assert
            expect($result)->toBe([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        });

        test('generates descending range from 10 to 1 with negative step', function (): void {
            // Arrange & Act
            $result = range(10, 1, -1);

            // Assert
            expect($result)->toBe([10, 9, 8, 7, 6, 5, 4, 3, 2, 1]);
        });

        test('generates range with custom step of 2', function (): void {
            // Arrange & Act
            $result = range(0, 10, 2);

            // Assert
            expect($result)->toBe([0, 2, 4, 6, 8, 10]);
        });

        test('generates range with custom step of 3', function (): void {
            // Arrange & Act
            $result = range(1, 10, 3);

            // Assert
            expect($result)->toBe([1, 4, 7, 10]);
        });

        test('generates descending range with custom negative step', function (): void {
            // Arrange & Act
            $result = range(20, 10, -2);

            // Assert
            expect($result)->toBe([20, 18, 16, 14, 12, 10]);
        });

        test('generates range with float values', function (): void {
            // Arrange & Act
            $result = range(1.5, 5.5);

            // Assert
            expect($result)->toBe([1.5, 2.5, 3.5, 4.5, 5.5]);
        });

        test('generates range with float start and integer end', function (): void {
            // Arrange & Act
            $result = range(1.5, 5);

            // Assert
            expect($result)->toBe([1.5, 2.5, 3.5, 4.5]);
        });

        test('generates single value range when start equals end', function (): void {
            // Arrange & Act
            $result = range(5, 5);

            // Assert
            expect($result)->toBe([5]);
        });

        test('generates single float value when start equals end', function (): void {
            // Arrange & Act
            $result = range(3.5, 3.5);

            // Assert
            expect($result)->toBe([3.5]);
        });

        test('generates range from zero', function (): void {
            // Arrange & Act
            $result = range(0, 5);

            // Assert
            expect($result)->toBe([0, 1, 2, 3, 4, 5]);
        });

        test('generates range to zero with negative step', function (): void {
            // Arrange & Act
            $result = range(5, 0, -1);

            // Assert
            expect($result)->toBe([5, 4, 3, 2, 1, 0]);
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception when step is zero', function (): void {
            // Arrange, Act & Assert
            expect(fn (): array => range(1, 10, 0))
                ->toThrow(InvalidArgumentException::class, 'Step cannot be zero');
        });

        test('throws exception for positive step with descending range', function (): void {
            // Arrange, Act & Assert
            expect(fn (): array => range(10, 1, 1))
                ->toThrow(InvalidArgumentException::class, 'Step direction does not match start/end range');
        });

        test('throws exception for positive step with descending range large values', function (): void {
            // Arrange, Act & Assert
            expect(fn (): array => range(100, 50, 5))
                ->toThrow(InvalidArgumentException::class, 'Step direction does not match start/end range');
        });

        test('throws exception for negative step with ascending range', function (): void {
            // Arrange, Act & Assert
            expect(fn (): array => range(1, 10, -1))
                ->toThrow(InvalidArgumentException::class, 'Step direction does not match start/end range');
        });

        test('throws exception for negative step with ascending range small values', function (): void {
            // Arrange, Act & Assert
            expect(fn (): array => range(1, 5, -2))
                ->toThrow(InvalidArgumentException::class, 'Step direction does not match start/end range');
        });

        test('throws exception for float zero step', function (): void {
            // Arrange, Act & Assert
            // Note: PHP's strict comparison (0.0 === 0) evaluates to true
            expect(fn (): array => range(1.0, 10.0, 0.0))
                ->toThrow(InvalidArgumentException::class, 'Step cannot be zero');
        });
    });

    describe('Edge Cases', function (): void {
        test('generates range with step larger than range', function (): void {
            // Arrange & Act
            $result = range(1, 5, 10);

            // Assert
            expect($result)->toBe([1]);
        });

        test('generates descending range with step larger than range', function (): void {
            // Arrange & Act
            $result = range(10, 5, -20);

            // Assert
            expect($result)->toBe([10]);
        });

        test('generates range with negative numbers ascending', function (): void {
            // Arrange & Act
            $result = range(-5, 5);

            // Assert
            expect($result)->toBe([-5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5]);
        });

        test('generates range with negative numbers descending', function (): void {
            // Arrange & Act
            $result = range(5, -5, -1);

            // Assert
            expect($result)->toBe([5, 4, 3, 2, 1, 0, -1, -2, -3, -4, -5]);
        });

        test('generates range between negative numbers', function (): void {
            // Arrange & Act
            $result = range(-10, -5);

            // Assert
            expect($result)->toBe([-10, -9, -8, -7, -6, -5]);
        });

        test('generates descending range between negative numbers', function (): void {
            // Arrange & Act
            $result = range(-5, -10, -1);

            // Assert
            expect($result)->toBe([-5, -6, -7, -8, -9, -10]);
        });

        test('generates range with float step values', function (): void {
            // Arrange & Act
            $result = range(0, 2, 0.5);

            // Assert
            // Note: First value is 0 (int) because $start is int, rest are floats
            expect($result)->toBe([0, 0.5, 1.0, 1.5, 2.0]);
        });

        test('generates descending range with float step values', function (): void {
            // Arrange & Act
            $result = range(2, 0, -0.5);

            // Assert
            // Note: First value is 2 (int) because $start is int, rest are floats
            expect($result)->toBe([2, 1.5, 1.0, 0.5, 0.0]);
        });

        test('generates range with very small step', function (): void {
            // Arrange & Act
            $result = range(0, 0.3, 0.1);

            // Assert
            // Note: Due to float precision, 0.3 is not reached (0 + 0.1 + 0.1 + 0.1 = 0.30000000000000004 > 0.3)
            expect($result)->toHaveCount(3);
            expect($result[0])->toBe(0);
            expect(abs($result[1] - 0.1))->toBeLessThan(0.000_1);
            expect(abs($result[2] - 0.2))->toBeLessThan(0.000_1);
        });

        test('generates range with negative float step', function (): void {
            // Arrange & Act
            $result = range(1.0, -1.0, -0.5);

            // Assert
            expect($result)->toBe([1.0, 0.5, 0.0, -0.5, -1.0]);
        });

        test('generates range with large numbers', function (): void {
            // Arrange & Act
            $result = range(1_000, 1_010);

            // Assert
            expect($result)->toBe([1_000, 1_001, 1_002, 1_003, 1_004, 1_005, 1_006, 1_007, 1_008, 1_009, 1_010]);
        });

        test('generates range with large step', function (): void {
            // Arrange & Act
            $result = range(0, 100, 25);

            // Assert
            expect($result)->toBe([0, 25, 50, 75, 100]);
        });

        test('generates range that does not end exactly on end value', function (): void {
            // Arrange & Act
            $result = range(0, 10, 3);

            // Assert
            expect($result)->toBe([0, 3, 6, 9]);
        });

        test('generates descending range that does not end exactly on end value', function (): void {
            // Arrange & Act
            $result = range(10, 0, -3);

            // Assert
            expect($result)->toBe([10, 7, 4, 1]);
        });

        test('generates range with very small negative start and end', function (): void {
            // Arrange & Act
            $result = range(-0.5, 0.5, 0.25);

            // Assert
            expect($result)->toBe([-0.5, -0.25, 0.0, 0.25, 0.5]);
        });

        test('generates range with mixed integer and float for step', function (): void {
            // Arrange & Act
            $result = range(0, 5, 1.5);

            // Assert
            // Note: First value is 0 (int) because $start is int, rest are floats
            expect($result)->toBe([0, 1.5, 3.0, 4.5]);
        });

        test('generates single value with large step size', function (): void {
            // Arrange & Act
            $result = range(1, 2, 100);

            // Assert
            expect($result)->toBe([1]);
        });

        test('generates negative range with positive step throws error', function (): void {
            // Arrange, Act & Assert
            expect(fn (): array => range(-10, -20, 1))
                ->toThrow(InvalidArgumentException::class, 'Step direction does not match start/end range');
        });
    });
});
