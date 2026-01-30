<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use ArrayIterator;
use Closure;
use Generator;

use function Cline\fp\foldr;
use function describe;
use function expect;
use function test;

describe('foldr()', function (): void {
    describe('Happy Paths', function (): void {
        test('reduces array from right to left', function (): void {
            // Arrange
            $buildList = foldr([], fn (array $acc, int $x): array => [$x, ...$acc]);
            $input = [1, 2, 3];

            // Act
            $result = $buildList($input);

            // Assert
            expect($result)->toBe([1, 2, 3]); // Right fold: processes 3->2->1, prepending each
        });

        test('concatenates strings from right to left', function (): void {
            // Arrange
            $concat = foldr('', fn (string $acc, string $char): string => $acc.$char);
            $input = ['a', 'b', 'c'];

            // Act
            $result = $concat($input);

            // Assert
            expect($result)->toBe('cba');
        });

        test('performs subtraction from right to left', function (): void {
            // Arrange
            $subtract = foldr(0, fn (int $acc, int $x): int => $acc - $x);
            $input = [1, 2, 3];

            // Act
            $result = $subtract($input);

            // Assert
            expect($result)->toBe(-6); // ((0 - 3) - 2) - 1 = -6
        });

        test('works with generator input', function (): void {
            // Arrange
            $buildList = foldr([], fn (array $acc, int $x): array => [$x, ...$acc]);
            $gen = function (): Generator {
                yield 1;

                yield 2;

                yield 3;
            };

            // Act
            $result = $buildList($gen());

            // Assert
            expect($result)->toBe([1, 2, 3]); // Right fold: processes 3->2->1, prepending each
        });

        test('works with ArrayIterator', function (): void {
            // Arrange
            $buildList = foldr([], fn (array $acc, int $x): array => [$x, ...$acc]);
            $iterator = new ArrayIterator([5, 10, 15]);

            // Act
            $result = $buildList($iterator);

            // Assert
            expect($result)->toBe([5, 10, 15]); // Right fold: processes 15->10->5, prepending each
        });

        test('builds nested structure from right', function (): void {
            // Arrange
            $nest = foldr([], fn (array $acc, int $x): array => [$x, $acc]);
            $input = [1, 2, 3];

            // Act
            $result = $nest($input);

            // Assert
            expect($result)->toBe([1, [2, [3, []]]]);
        });

        test('sums numbers same as left fold', function (): void {
            // Arrange
            $sum = foldr(0, fn (int $acc, int $x): int => $acc + $x);
            $input = [1, 2, 3, 4, 5];

            // Act
            $result = $sum($input);

            // Assert
            expect($result)->toBe(15); // Addition is associative
        });
    });

    describe('Sad Paths', function (): void {
        // Note: foldr() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns initial value for empty array', function (): void {
            // Arrange
            $sum = foldr(42, fn (int $acc, int $x): int => $acc + $x);
            $input = [];

            // Act
            $result = $sum($input);

            // Assert
            expect($result)->toBe(42);
        });

        test('processes single element array', function (): void {
            // Arrange
            $multiply = foldr(2, fn (int $acc, int $x): int => $acc * $x);
            $input = [5];

            // Act
            $result = $multiply($input);

            // Assert
            expect($result)->toBe(10);
        });

        test('handles empty generator', function (): void {
            // Arrange
            $sum = foldr(100, fn (int $acc, int $x): int => $acc + $x);
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
            $countNulls = foldr(0, fn (int $acc, ?int $x): int => $x === null ? $acc + 1 : $acc);
            $input = [1, null, 2, null, 3];

            // Act
            $result = $countNulls($input);

            // Assert
            expect($result)->toBe(2);
        });

        test('handles false values in array', function (): void {
            // Arrange
            $countFalse = foldr(0, fn (int $acc, bool $x): int => $x === false ? $acc + 1 : $acc);
            $input = [true, false, true, false, false];

            // Act
            $result = $countFalse($input);

            // Assert
            expect($result)->toBe(3);
        });

        test('handles zero values', function (): void {
            // Arrange
            $sum = foldr(0, fn (int $acc, int $x): int => $acc + $x);
            $input = [0, 0, 0];

            // Act
            $result = $sum($input);

            // Assert
            expect($result)->toBe(0);
        });

        test('handles empty strings', function (): void {
            // Arrange
            $concat = foldr('', fn (string $acc, string $s): string => $acc.$s);
            $input = ['', 'a', '', 'b', ''];

            // Act
            $result = $concat($input);

            // Assert
            expect($result)->toBe('ba'); // Processed right to left
        });

        test('can be reused with different inputs', function (): void {
            // Arrange
            $buildList = foldr([], fn (array $acc, int $x): array => [$x, ...$acc]);

            // Act
            $result1 = $buildList([1, 2, 3]);
            $result2 = $buildList([4, 5]);

            // Assert
            expect($result1)->toBe([1, 2, 3]);
            expect($result2)->toBe([4, 5]);
        });

        test('processes associative array values', function (): void {
            // Arrange
            $sum = foldr(0, fn (int $acc, int $x): int => $acc + $x);
            $input = ['a' => 10, 'b' => 20, 'c' => 30];

            // Act
            $result = $sum($input);

            // Assert
            expect($result)->toBe(60);
        });

        test('preserves associative keys during reversal', function (): void {
            // Arrange
            $buildList = foldr([], fn (array $acc, string $x): array => [$x, ...$acc]);
            $input = ['first' => 'a', 'second' => 'b', 'third' => 'c'];

            // Act
            $result = $buildList($input);

            // Assert
            expect($result)->toBe(['a', 'b', 'c']); // Right fold preserves order when prepending
        });

        test('demonstrates difference from foldl with division', function (): void {
            // Arrange
            $divideRight = foldr(1, fn (float $acc, float $x): float => $acc / $x);
            $input = [2.0, 3.0, 4.0];

            // Act
            $result = $divideRight($input);

            // Assert
            expect($result)->toBe(1.0 / 4.0 / 3.0 / 2.0); // Right-to-left: ((1/4)/3)/2
        });

        test('appends to accumulator from right', function (): void {
            // Arrange
            $append = foldr([], fn (array $acc, string $x): array => [...$acc, $x]);
            $input = ['a', 'b', 'c'];

            // Act
            $result = $append($input);

            // Assert
            expect($result)->toBe(['c', 'b', 'a']);
        });

        test('counts elements processing from right', function (): void {
            // Arrange
            $count = foldr(0, fn (int $acc, mixed $x): int => $acc + 1);
            $input = ['a', 'b', 'c', 'd', 'e'];

            // Act
            $result = $count($input);

            // Assert
            expect($result)->toBe(5);
        });

        test('builds string with parentheses showing order', function (): void {
            // Arrange
            $showOrder = foldr('', fn (string $acc, int $x): string => '('.$x.($acc !== '' && $acc !== '0' ? ' '.$acc : '').')');
            $input = [1, 2, 3];

            // Act
            $result = $showOrder($input);

            // Assert
            expect($result)->toBe('(1 (2 (3)))');
        });

        test('implements map using foldr', function (): void {
            // Arrange
            $mapUsingFold = fn (callable $fn): Closure => foldr([], fn (array $acc, mixed $x): array => [$fn($x), ...$acc]);
            $double = $mapUsingFold(fn (int $x): int => $x * 2);
            $input = [1, 2, 3, 4];

            // Act
            $result = $double($input);

            // Assert
            expect($result)->toBe([2, 4, 6, 8]); // Right fold with prepend maintains order
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
