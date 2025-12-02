<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Generator;

use const PHP_INT_MIN;

use function Cline\fp\foldl;
use function Cline\fp\reduce;
use function describe;
use function expect;
use function max;
use function test;

describe('foldl()', function (): void {
    describe('Happy Paths', function (): void {
        test('reduces array to sum from left to right', function (): void {
            // Arrange
            $sum = foldl(0, fn (int $acc, int $x): int => $acc + $x);
            $input = [1, 2, 3, 4, 5];

            // Act
            $result = $sum($input);

            // Assert
            expect($result)->toBe(15);
        });

        test('builds string from left to right', function (): void {
            // Arrange
            $concat = foldl('', fn (string $acc, string $char): string => $acc.$char);
            $input = ['a', 'b', 'c'];

            // Act
            $result = $concat($input);

            // Assert
            expect($result)->toBe('abc');
        });

        test('builds array from left to right', function (): void {
            // Arrange
            $collect = foldl([], fn (array $acc, int $x): array => [...$acc, $x * 2]);
            $input = [1, 2, 3];

            // Act
            $result = $collect($input);

            // Assert
            expect($result)->toBe([2, 4, 6]);
        });

        test('works with generator input', function (): void {
            // Arrange
            $sum = foldl(0, fn (int $acc, int $x): int => $acc + $x);
            $gen = function (): Generator {
                yield 1;

                yield 2;

                yield 3;
            };

            // Act
            $result = $sum($gen());

            // Assert
            expect($result)->toBe(6);
        });

        test('performs subtraction in left-to-right order', function (): void {
            // Arrange
            $subtract = foldl(10, fn (int $acc, int $x): int => $acc - $x);
            $input = [1, 2, 3];

            // Act
            $result = $subtract($input);

            // Assert
            expect($result)->toBe(4); // ((10 - 1) - 2) - 3 = 4
        });

        test('works as alias for reduce', function (): void {
            // Arrange
            $foldlSum = foldl(0, fn (int $acc, int $x): int => $acc + $x);
            $reduceSum = reduce(0, fn (int $acc, int $x): int => $acc + $x);
            $input = [1, 2, 3, 4, 5];

            // Act
            $foldlResult = $foldlSum($input);
            $reduceResult = $reduceSum($input);

            // Assert
            expect($foldlResult)->toBe($reduceResult);
            expect($foldlResult)->toBe(15);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: foldl() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns initial value for empty array', function (): void {
            // Arrange
            $sum = foldl(42, fn (int $acc, int $x): int => $acc + $x);
            $input = [];

            // Act
            $result = $sum($input);

            // Assert
            expect($result)->toBe(42);
        });

        test('processes single element array', function (): void {
            // Arrange
            $multiply = foldl(2, fn (int $acc, int $x): int => $acc * $x);
            $input = [5];

            // Act
            $result = $multiply($input);

            // Assert
            expect($result)->toBe(10);
        });

        test('handles empty generator', function (): void {
            // Arrange
            $sum = foldl(100, fn (int $acc, int $x): int => $acc + $x);
            $gen = function (): Generator {
                return;

                yield;
            };

            // Act
            $result = $sum($gen());

            // Assert
            expect($result)->toBe(100);
        });

        test('handles null values in array', function (): void {
            // Arrange
            $countNulls = foldl(0, fn (int $acc, ?int $x): int => $x === null ? $acc + 1 : $acc);
            $input = [1, null, 2, null, 3];

            // Act
            $result = $countNulls($input);

            // Assert
            expect($result)->toBe(2);
        });

        test('handles false values in array', function (): void {
            // Arrange
            $countFalse = foldl(0, fn (int $acc, bool $x): int => $x === false ? $acc + 1 : $acc);
            $input = [true, false, true, false, false];

            // Act
            $result = $countFalse($input);

            // Assert
            expect($result)->toBe(3);
        });

        test('handles zero values', function (): void {
            // Arrange
            $sum = foldl(0, fn (int $acc, int $x): int => $acc + $x);
            $input = [0, 0, 0];

            // Act
            $result = $sum($input);

            // Assert
            expect($result)->toBe(0);
        });

        test('handles empty strings', function (): void {
            // Arrange
            $concat = foldl('', fn (string $acc, string $s): string => $acc.$s);
            $input = ['', 'a', '', 'b', ''];

            // Act
            $result = $concat($input);

            // Assert
            expect($result)->toBe('ab');
        });

        test('can be reused with different inputs', function (): void {
            // Arrange
            $product = foldl(1, fn (int $acc, int $x): int => $acc * $x);

            // Act
            $result1 = $product([2, 3, 4]);
            $result2 = $product([5, 6]);

            // Assert
            expect($result1)->toBe(24);
            expect($result2)->toBe(30);
        });

        test('builds nested structures', function (): void {
            // Arrange
            $nest = foldl([], fn (array $acc, int $x): array => [$acc, $x]);
            $input = [1, 2, 3];

            // Act
            $result = $nest($input);

            // Assert
            expect($result)->toBe([[[[], 1], 2], 3]);
        });

        test('processes associative array values', function (): void {
            // Arrange
            $sum = foldl(0, fn (int $acc, int $x): int => $acc + $x);
            $input = ['a' => 10, 'b' => 20, 'c' => 30];

            // Act
            $result = $sum($input);

            // Assert
            expect($result)->toBe(60);
        });

        test('counts elements using fold', function (): void {
            // Arrange
            $count = foldl(0, fn (int $acc, mixed $x): int => $acc + 1);
            $input = ['a', 'b', 'c', 'd', 'e'];

            // Act
            $result = $count($input);

            // Assert
            expect($result)->toBe(5);
        });

        test('finds maximum value', function (): void {
            // Arrange
            $max = foldl(PHP_INT_MIN, fn (int $acc, int $x): int => max($x, $acc));
            $input = [3, 7, 2, 9, 1];

            // Act
            $result = $max($input);

            // Assert
            expect($result)->toBe(9);
        });

        test('reverses array using fold', function (): void {
            // Arrange
            $reverse = foldl([], fn (array $acc, mixed $x): array => [$x, ...$acc]);
            $input = [1, 2, 3, 4, 5];

            // Act
            $result = $reverse($input);

            // Assert
            expect($result)->toBe([5, 4, 3, 2, 1]);
        });
    });

    describe('Regressions', function (): void {
        // Only include tests for documented bugs with ticket references
        // Example structure for future regression tests:
        // test('prevents X bug that caused Y [TICKET-123]', function (): void {
        //     // Test implementation
        // });
    });
});
