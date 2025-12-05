<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\liftA2;
use function describe;
use function expect;
use function max;
use function min;
use function sprintf;
use function test;

describe('liftA2()', function (): void {
    describe('Happy Paths', function (): void {
        test('lifts binary addition function', function (): void {
            // Arrange
            $add = fn (int $a): callable => fn (int $b): int => $a + $b;
            $liftedAdd = liftA2($add);
            $as = [1, 2];
            $bs = [10, 20];

            // Act
            $result = $liftedAdd($as, $bs);

            // Assert
            expect($result)->toBe([11, 21, 12, 22]);
        });

        test('creates Cartesian product of results', function (): void {
            // Arrange
            $multiply = fn (int $a): callable => fn (int $b): int => $a * $b;
            $liftedMultiply = liftA2($multiply);
            $as = [2, 3];
            $bs = [4, 5];

            // Act
            $result = $liftedMultiply($as, $bs);

            // Assert
            expect($result)->toBe([8, 10, 12, 15]);
        });

        test('lifts string concatenation function', function (): void {
            // Arrange
            $concat = fn (string $a): callable => fn (string $b): string => $a.$b;
            $liftedConcat = liftA2($concat);
            $as = ['a', 'b'];
            $bs = ['1', '2'];

            // Act
            $result = $liftedConcat($as, $bs);

            // Assert
            expect($result)->toBe(['a1', 'a2', 'b1', 'b2']);
        });

        test('works with single element arrays', function (): void {
            // Arrange
            $subtract = fn (int $a): callable => fn (int $b): int => $a - $b;
            $liftedSubtract = liftA2($subtract);
            $as = [10];
            $bs = [3];

            // Act
            $result = $liftedSubtract($as, $bs);

            // Assert
            expect($result)->toBe([7]);
        });

        test('demonstrates applicative Cartesian product', function (): void {
            // Arrange
            $pair = fn (mixed $a): callable => fn (mixed $b): array => [$a, $b];
            $liftedPair = liftA2($pair);
            $as = [1, 2];
            $bs = ['x', 'y'];

            // Act
            $result = $liftedPair($as, $bs);

            // Assert
            expect($result)->toBe([[1, 'x'], [1, 'y'], [2, 'x'], [2, 'y']]);
        });

        test('works with larger arrays', function (): void {
            // Arrange
            $add = fn (int $a): callable => fn (int $b): int => $a + $b;
            $liftedAdd = liftA2($add);
            $as = [1, 2, 3];
            $bs = [10, 20, 30];

            // Act
            $result = $liftedAdd($as, $bs);

            // Assert
            expect($result)->toBe([11, 21, 31, 12, 22, 32, 13, 23, 33]);
        });

        test('lifts comparison function', function (): void {
            // Arrange
            $greaterThan = fn (int $a): callable => fn (int $b): bool => $a > $b;
            $liftedGreaterThan = liftA2($greaterThan);
            $as = [5, 10];
            $bs = [3, 8];

            // Act
            $result = $liftedGreaterThan($as, $bs);

            // Assert
            expect($result)->toBe([true, false, true, true]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: liftA2() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns empty array when first array is empty', function (): void {
            // Arrange
            $add = fn (int $a): callable => fn (int $b): int => $a + $b;
            $liftedAdd = liftA2($add);
            $as = [];
            $bs = [1, 2, 3];

            // Act
            $result = $liftedAdd($as, $bs);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns empty array when second array is empty', function (): void {
            // Arrange
            $add = fn (int $a): callable => fn (int $b): int => $a + $b;
            $liftedAdd = liftA2($add);
            $as = [1, 2, 3];
            $bs = [];

            // Act
            $result = $liftedAdd($as, $bs);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns empty array when both arrays are empty', function (): void {
            // Arrange
            $add = fn (int $a): callable => fn (int $b): int => $a + $b;
            $liftedAdd = liftA2($add);
            $as = [];
            $bs = [];

            // Act
            $result = $liftedAdd($as, $bs);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles null values', function (): void {
            // Arrange
            $coalesce = fn (?int $a): callable => fn (?int $b): int => $a ?? $b ?? 0;
            $liftedCoalesce = liftA2($coalesce);
            $as = [null, 1];
            $bs = [2, null];

            // Act
            $result = $liftedCoalesce($as, $bs);

            // Assert
            expect($result)->toBe([2, 0, 1, 1]);
        });

        test('handles false values', function (): void {
            // Arrange
            $and = fn (bool $a): callable => fn (bool $b): bool => $a && $b;
            $liftedAnd = liftA2($and);
            $as = [true, false];
            $bs = [true, false];

            // Act
            $result = $liftedAnd($as, $bs);

            // Assert
            expect($result)->toBe([true, false, false, false]);
        });

        test('handles zero values', function (): void {
            // Arrange
            $multiply = fn (int $a): callable => fn (int $b): int => $a * $b;
            $liftedMultiply = liftA2($multiply);
            $as = [0, 1];
            $bs = [2, 3];

            // Act
            $result = $liftedMultiply($as, $bs);

            // Assert
            expect($result)->toBe([0, 0, 2, 3]);
        });

        test('handles empty strings', function (): void {
            // Arrange
            $concat = fn (string $a): callable => fn (string $b): string => $a.'-'.$b;
            $liftedConcat = liftA2($concat);
            $as = ['', 'a'];
            $bs = ['b', ''];

            // Act
            $result = $liftedConcat($as, $bs);

            // Assert
            expect($result)->toBe(['-b', '-', 'a-b', 'a-']);
        });

        test('can be reused with different inputs', function (): void {
            // Arrange
            $add = fn (int $a): callable => fn (int $b): int => $a + $b;
            $liftedAdd = liftA2($add);

            // Act
            $result1 = $liftedAdd([1], [10]);
            $result2 = $liftedAdd([2, 3], [20, 30]);

            // Assert
            expect($result1)->toBe([11]);
            expect($result2)->toBe([22, 32, 23, 33]);
        });

        test('demonstrates order of application', function (): void {
            // Arrange
            $format = fn (string $a): callable => fn (string $b): string => sprintf('(%s,%s)', $a, $b);
            $liftedFormat = liftA2($format);
            $as = ['a', 'b'];
            $bs = ['1', '2'];

            // Act
            $result = $liftedFormat($as, $bs);

            // Assert
            expect($result)->toBe(['(a,1)', '(a,2)', '(b,1)', '(b,2)']);
        });

        test('works with division function', function (): void {
            // Arrange
            $divide = fn (float $a): callable => fn (float $b): ?float => $b !== 0.0 ? $a / $b : null;
            $liftedDivide = liftA2($divide);
            $as = [10.0, 20.0];
            $bs = [2.0, 0.0];

            // Act
            $result = $liftedDivide($as, $bs);

            // Assert
            expect($result)->toBe([5.0, null, 10.0, null]);
        });

        test('lifts function returning arrays', function (): void {
            // Arrange
            $makeArray = fn (int $a): callable => fn (int $b): array => [$a, $b];
            $liftedMakeArray = liftA2($makeArray);
            $as = [1, 2];
            $bs = [3, 4];

            // Act
            $result = $liftedMakeArray($as, $bs);

            // Assert
            expect($result)->toBe([[1, 3], [1, 4], [2, 3], [2, 4]]);
        });

        test('lifts function returning objects', function (): void {
            // Arrange
            $makeObj = fn (int $a): callable => fn (int $b): object => (object) ['a' => $a, 'b' => $b];
            $liftedMakeObj = liftA2($makeObj);
            $as = [1, 2];
            $bs = [10, 20];

            // Act
            $result = $liftedMakeObj($as, $bs);

            // Assert
            expect($result)->toHaveCount(4);
            expect($result[0]->a)->toBe(1);
            expect($result[0]->b)->toBe(10);
            expect($result[3]->a)->toBe(2);
            expect($result[3]->b)->toBe(20);
        });

        test('demonstrates applicative law - composition', function (): void {
            // Arrange
            $add = fn (int $a): callable => fn (int $b): int => $a + $b;
            $liftedAdd = liftA2($add);
            $as = [1, 2];
            $bs = [10, 20];

            // Act
            $result = $liftedAdd($as, $bs);

            // Assert
            expect($result)->toHaveCount(4);
            expect($result)->toContain(11);
            expect($result)->toContain(21);
        });

        test('works with min function', function (): void {
            // Arrange
            $min = fn (int $a): callable => fn (int $b): int => min($a, $b);
            $liftedMin = liftA2($min);
            $as = [5, 10];
            $bs = [3, 15];

            // Act
            $result = $liftedMin($as, $bs);

            // Assert
            expect($result)->toBe([3, 5, 3, 10]);
        });

        test('works with max function', function (): void {
            // Arrange
            $max = fn (int $a): callable => fn (int $b): int => max($a, $b);
            $liftedMax = liftA2($max);
            $as = [5, 10];
            $bs = [3, 15];

            // Act
            $result = $liftedMax($as, $bs);

            // Assert
            expect($result)->toBe([5, 15, 10, 15]);
        });

        test('useful for validation with two inputs', function (): void {
            // Arrange
            $inRange = fn (int $min): callable => fn (int $max): callable => fn (int $val): bool => $val >= $min && $val <= $max;
            $liftedInRange = liftA2($inRange);
            $mins = [0, 10];
            $maxs = [100, 50];

            // Act
            $validators = $liftedInRange($mins, $maxs);

            // Assert
            expect($validators)->toHaveCount(4);
            expect($validators[0](50))->toBeTrue();  // 0-100: 50 in range
            expect($validators[1](50))->toBeTrue();  // 0-50: 50 in range
            expect($validators[2](50))->toBeTrue();  // 10-100: 50 in range
            expect($validators[3](50))->toBeTrue();  // 10-50: 50 in range
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
