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

use function Cline\fp\reduce;
use function Cline\fp\scan;
use function describe;
use function end;
use function expect;
use function max;
use function min;
use function test;

describe('scan()', function (): void {
    describe('Happy Paths', function (): void {
        test('creates running sum of numbers', function (): void {
            // Arrange
            $runningSum = scan(0, fn (int $acc, int $n): int => $acc + $n);
            $input = [1, 2, 3, 4];

            // Act
            $result = $runningSum($input);

            // Assert
            expect($result)->toBe([0, 1, 3, 6, 10]);
        });

        test('creates running product', function (): void {
            // Arrange
            $runningProduct = scan(1, fn (int $acc, int $n): int => $acc * $n);
            $input = [2, 3, 4];

            // Act
            $result = $runningProduct($input);

            // Assert
            expect($result)->toBe([1, 2, 6, 24]);
        });

        test('accumulates strings', function (): void {
            // Arrange
            $accumulate = scan('', fn (string $acc, string $s): string => $acc.$s);
            $input = ['a', 'b', 'c'];

            // Act
            $result = $accumulate($input);

            // Assert
            expect($result)->toBe(['', 'a', 'ab', 'abc']);
        });

        test('builds arrays progressively', function (): void {
            // Arrange
            $buildArray = scan([], fn (array $acc, int $n): array => [...$acc, $n]);
            $input = [1, 2, 3];

            // Act
            $result = $buildArray($input);

            // Assert
            expect($result)->toBe([[], [1], [1, 2], [1, 2, 3]]);
        });

        test('works with generator', function (): void {
            // Arrange
            $runningSum = scan(0, fn (int $acc, int $n): int => $acc + $n);
            $gen = function (): Generator {
                yield 10;

                yield 20;

                yield 30;
            };

            // Act
            $result = $runningSum($gen());

            // Assert
            expect($result)->toBe([0, 10, 30, 60]);
        });

        test('works with ArrayIterator', function (): void {
            // Arrange
            $runningSum = scan(0, fn (int $acc, int $n): int => $acc + $n);
            $iterator = new ArrayIterator([5, 10, 15]);

            // Act
            $result = $runningSum($iterator);

            // Assert
            expect($result)->toBe([0, 5, 15, 30]);
        });

        test('tracks maximum value seen', function (): void {
            // Arrange
            $trackMax = scan(0, fn (int $max, int $n): int => max($max, $n));
            $input = [3, 1, 4, 1, 5, 9, 2];

            // Act
            $result = $trackMax($input);

            // Assert
            expect($result)->toBe([0, 3, 3, 4, 4, 5, 9, 9]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: scan() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns only initial value for empty array', function (): void {
            // Arrange
            $runningSum = scan(0, fn (int $acc, int $n): int => $acc + $n);
            $input = [];

            // Act
            $result = $runningSum($input);

            // Assert
            expect($result)->toBe([0]);
        });

        test('returns only initial value for empty generator', function (): void {
            // Arrange
            $runningSum = scan(0, fn (int $acc, int $n): int => $acc + $n);
            $gen = function (): Generator {
                return;

                yield;
            };

            // Act
            $result = $runningSum($gen());

            // Assert
            expect($result)->toBe([0]);
        });

        test('handles single element array', function (): void {
            // Arrange
            $runningSum = scan(0, fn (int $acc, int $n): int => $acc + $n);
            $input = [42];

            // Act
            $result = $runningSum($input);

            // Assert
            expect($result)->toBe([0, 42]);
        });

        test('works with null initial value', function (): void {
            // Arrange
            $findFirst = scan(null, fn (?int $acc, int $n): int => $acc ?? $n);
            $input = [1, 2, 3];

            // Act
            $result = $findFirst($input);

            // Assert
            expect($result)->toBe([null, 1, 1, 1]);
        });

        test('works with false initial value', function (): void {
            // Arrange
            $anyTrue = scan(false, fn (bool $acc, bool $n): bool => $acc || $n);
            $input = [false, false, true, false];

            // Act
            $result = $anyTrue($input);

            // Assert
            expect($result)->toBe([false, false, false, true, true]);
        });

        test('works with zero initial value', function (): void {
            // Arrange
            $runningSum = scan(0, fn (int $acc, int $n): int => $acc + $n);
            $input = [1, 2, 3];

            // Act
            $result = $runningSum($input);

            // Assert
            expect($result)->toBe([0, 1, 3, 6]);
        });

        test('works with empty string initial value', function (): void {
            // Arrange
            $accumulate = scan('', fn (string $acc, string $s): string => $acc.$s);
            $input = ['a', 'b'];

            // Act
            $result = $accumulate($input);

            // Assert
            expect($result)->toBe(['', 'a', 'ab']);
        });

        test('tracks running minimum', function (): void {
            // Arrange
            $trackMin = scan(PHP_INT_MAX, fn (int $min, int $n): int => min($min, $n));
            $input = [5, 2, 8, 1, 9];

            // Act
            $result = $trackMin($input);

            // Assert
            expect($result[0])->toBe(PHP_INT_MAX);
            expect($result[1])->toBe(5);
            expect($result[4])->toBe(1);
        });

        test('counts elements cumulatively', function (): void {
            // Arrange
            $count = scan(0, fn (int $acc, mixed $n): int => $acc + 1);
            $input = ['a', 'b', 'c', 'd'];

            // Act
            $result = $count($input);

            // Assert
            expect($result)->toBe([0, 1, 2, 3, 4]);
        });

        test('can be reused with different inputs', function (): void {
            // Arrange
            $runningSum = scan(0, fn (int $acc, int $n): int => $acc + $n);

            // Act
            $result1 = $runningSum([1, 2, 3]);
            $result2 = $runningSum([10, 20]);

            // Assert
            expect($result1)->toBe([0, 1, 3, 6]);
            expect($result2)->toBe([0, 10, 30]);
        });

        test('handles negative numbers', function (): void {
            // Arrange
            $runningSum = scan(0, fn (int $acc, int $n): int => $acc + $n);
            $input = [-1, -2, -3];

            // Act
            $result = $runningSum($input);

            // Assert
            expect($result)->toBe([0, -1, -3, -6]);
        });

        test('builds object state progressively', function (): void {
            // Arrange
            $buildState = scan(
                (object) ['total' => 0, 'count' => 0],
                fn (object $acc, int $n): object => (object) [
                    'total' => $acc->total + $n,
                    'count' => $acc->count + 1,
                ],
            );
            $input = [10, 20, 30];

            // Act
            $result = $buildState($input);

            // Assert
            expect($result)->toHaveCount(4);
            expect($result[3]->total)->toBe(60);
            expect($result[3]->count)->toBe(3);
        });

        test('useful for tracking state changes', function (): void {
            // Arrange
            $balance = scan(100, fn (int $balance, int $transaction): int => $balance + $transaction);
            $transactions = [50, -20, -10, 100];

            // Act
            $result = $balance($transactions);

            // Assert
            expect($result)->toBe([100, 150, 130, 120, 220]);
        });

        test('different from reduce by including intermediate values', function (): void {
            // Arrange
            $input = [1, 2, 3, 4];
            $reducer = fn (int $acc, int $n): int => $acc + $n;
            $scanResult = scan(0, $reducer)($input);
            $reduceResult = reduce(0, $reducer)($input);

            // Act & Assert
            expect($scanResult)->toBe([0, 1, 3, 6, 10]);
            expect($reduceResult)->toBe(10); // Only final value
            expect(end($scanResult))->toBe($reduceResult);
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
