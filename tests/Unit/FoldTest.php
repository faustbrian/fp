<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use ArrayIterator;
use Generator;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

use function Cline\fp\fold;
use function Cline\fp\reduce;
use function describe;
use function expect;
use function max;
use function min;
use function test;

describe('fold()', function (): void {
    describe('Happy Paths', function (): void {
        test('sums array of numbers', function (): void {
            // Arrange
            $sum = fold(0, fn (int $acc, int $n): int => $acc + $n);
            $input = [1, 2, 3, 4];

            // Act
            $result = $sum($input);

            // Assert
            expect($result)->toBe(10);
        });

        test('multiplies array of numbers', function (): void {
            // Arrange
            $product = fold(1, fn (int $acc, int $n): int => $acc * $n);
            $input = [2, 3, 4];

            // Act
            $result = $product($input);

            // Assert
            expect($result)->toBe(24);
        });

        test('concatenates strings', function (): void {
            // Arrange
            $concat = fold('', fn (string $acc, string $s): string => $acc.$s);
            $input = ['hello', ' ', 'world'];

            // Act
            $result = $concat($input);

            // Assert
            expect($result)->toBe('hello world');
        });

        test('builds array from values', function (): void {
            // Arrange
            $toArray = fold([], fn (array $acc, int $n): array => [...$acc, $n * 2]);
            $input = [1, 2, 3];

            // Act
            $result = $toArray($input);

            // Assert
            expect($result)->toBe([2, 4, 6]);
        });

        test('works with generator', function (): void {
            // Arrange
            $sum = fold(0, fn (int $acc, int $n): int => $acc + $n);
            $gen = function (): Generator {
                yield 10;

                yield 20;

                yield 30;
            };

            // Act
            $result = $sum($gen());

            // Assert
            expect($result)->toBe(60);
        });

        test('works with ArrayIterator', function (): void {
            // Arrange
            $sum = fold(0, fn (int $acc, int $n): int => $acc + $n);
            $iterator = new ArrayIterator([5, 10, 15]);

            // Act
            $result = $sum($iterator);

            // Assert
            expect($result)->toBe(30);
        });

        test('finds maximum value', function (): void {
            // Arrange
            $findMax = fold(PHP_INT_MIN, fn (int $max, int $n): int => max($max, $n));
            $input = [3, 1, 4, 1, 5, 9, 2];

            // Act
            $result = $findMax($input);

            // Assert
            expect($result)->toBe(9);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: fold() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns initial value for empty array', function (): void {
            // Arrange
            $sum = fold(0, fn (int $acc, int $n): int => $acc + $n);
            $input = [];

            // Act
            $result = $sum($input);

            // Assert
            expect($result)->toBe(0);
        });

        test('returns initial value for empty generator', function (): void {
            // Arrange
            $sum = fold(0, fn (int $acc, int $n): int => $acc + $n);
            $gen = function (): Generator {
                return;

                yield;
            };

            // Act
            $result = $sum($gen());

            // Assert
            expect($result)->toBe(0);
        });

        test('handles single element array', function (): void {
            // Arrange
            $sum = fold(0, fn (int $acc, int $n): int => $acc + $n);
            $input = [42];

            // Act
            $result = $sum($input);

            // Assert
            expect($result)->toBe(42);
        });

        test('works with null initial value', function (): void {
            // Arrange
            $findFirst = fold(null, fn (?int $acc, int $n): int => $acc ?? $n);
            $input = [1, 2, 3];

            // Act
            $result = $findFirst($input);

            // Assert
            expect($result)->toBe(1);
        });

        test('works with false initial value', function (): void {
            // Arrange
            $anyTrue = fold(false, fn (bool $acc, bool $n): bool => $acc || $n);
            $input = [false, false, true, false];

            // Act
            $result = $anyTrue($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('works with zero initial value', function (): void {
            // Arrange
            $sum = fold(0, fn (int $acc, int $n): int => $acc + $n);
            $input = [1, 2, 3];

            // Act
            $result = $sum($input);

            // Assert
            expect($result)->toBe(6);
        });

        test('works with empty string initial value', function (): void {
            // Arrange
            $join = fold('', fn (string $acc, string $s): string => $acc.$s);
            $input = ['a', 'b', 'c'];

            // Act
            $result = $join($input);

            // Assert
            expect($result)->toBe('abc');
        });

        test('finds minimum value', function (): void {
            // Arrange
            $findMin = fold(PHP_INT_MAX, fn (int $min, int $n): int => min($min, $n));
            $input = [5, 2, 8, 1, 9];

            // Act
            $result = $findMin($input);

            // Assert
            expect($result)->toBe(1);
        });

        test('counts elements', function (): void {
            // Arrange
            $count = fold(0, fn (int $acc, mixed $n): int => $acc + 1);
            $input = ['a', 'b', 'c', 'd'];

            // Act
            $result = $count($input);

            // Assert
            expect($result)->toBe(4);
        });

        test('can be reused with different inputs', function (): void {
            // Arrange
            $sum = fold(0, fn (int $acc, int $n): int => $acc + $n);

            // Act
            $result1 = $sum([1, 2, 3]);
            $result2 = $sum([10, 20]);

            // Assert
            expect($result1)->toBe(6);
            expect($result2)->toBe(30);
        });

        test('handles negative numbers', function (): void {
            // Arrange
            $sum = fold(0, fn (int $acc, int $n): int => $acc + $n);
            $input = [-1, -2, -3];

            // Act
            $result = $sum($input);

            // Assert
            expect($result)->toBe(-6);
        });

        test('builds complex object', function (): void {
            // Arrange
            $buildStats = fold(
                ['sum' => 0, 'count' => 0],
                fn (array $acc, int $n): array => [
                    'sum' => $acc['sum'] + $n,
                    'count' => $acc['count'] + 1,
                ],
            );
            $input = [10, 20, 30];

            // Act
            $result = $buildStats($input);

            // Assert
            expect($result)->toBe(['sum' => 60, 'count' => 3]);
        });

        test('reverses array using fold', function (): void {
            // Arrange
            $reverse = fold([], fn (array $acc, mixed $n): array => [$n, ...$acc]);
            $input = [1, 2, 3, 4];

            // Act
            $result = $reverse($input);

            // Assert
            expect($result)->toBe([4, 3, 2, 1]);
        });

        test('works as alias for reduce', function (): void {
            // Arrange
            $input = [1, 2, 3, 4];
            $reducer = fn (int $acc, int $n): int => $acc + $n;
            $foldResult = fold(0, $reducer);
            $reduceResult = reduce(0, $reducer);

            // Act
            $folded = $foldResult($input);
            $reduced = $reduceResult($input);

            // Assert
            expect($folded)->toBe($reduced);
            expect($folded)->toBe(10);
        });

        test('groups items by property', function (): void {
            // Arrange
            $groupBy = fold(
                [],
                function (array $acc, array $item): array {
                    $key = $item['type'];
                    $acc[$key] ??= [];
                    $acc[$key][] = $item;

                    return $acc;
                },
            );
            $input = [
                ['type' => 'A', 'value' => 1],
                ['type' => 'B', 'value' => 2],
                ['type' => 'A', 'value' => 3],
            ];

            // Act
            $result = $groupBy($input);

            // Assert
            expect($result)->toHaveKey('A');
            expect($result)->toHaveKey('B');
            expect($result['A'])->toHaveCount(2);
            expect($result['B'])->toHaveCount(1);
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
