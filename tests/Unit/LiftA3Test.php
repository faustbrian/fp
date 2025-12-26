<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\liftA3;
use function describe;
use function expect;
use function max;
use function min;
use function sprintf;
use function test;

describe('liftA3()', function (): void {
    describe('Happy Paths', function (): void {
        test('lifts ternary addition function', function (): void {
            // Arrange
            $sum3 = fn (int $a): callable => fn (int $b): callable => fn (int $c): int => $a + $b + $c;
            $liftedSum = liftA3($sum3);
            $as = [1, 2];
            $bs = [10, 20];
            $cs = [100];

            // Act
            $result = $liftedSum($as, $bs, $cs);

            // Assert
            expect($result)->toBe([111, 121, 112, 122]);
        });

        test('creates Cartesian product of three arrays', function (): void {
            // Arrange
            $multiply3 = fn (int $a): callable => fn (int $b): callable => fn (int $c): int => $a * $b * $c;
            $liftedMultiply = liftA3($multiply3);
            $as = [1, 2];
            $bs = [3, 4];
            $cs = [5, 6];

            // Act
            $result = $liftedMultiply($as, $bs, $cs);

            // Assert
            expect($result)->toBe([15, 18, 20, 24, 30, 36, 40, 48]);
        });

        test('lifts string concatenation function', function (): void {
            // Arrange
            $concat3 = fn (string $a): callable => fn (string $b): callable => fn (string $c): string => $a.$b.$c;
            $liftedConcat = liftA3($concat3);
            $as = ['a'];
            $bs = ['1'];
            $cs = ['x', 'y'];

            // Act
            $result = $liftedConcat($as, $bs, $cs);

            // Assert
            expect($result)->toBe(['a1x', 'a1y']);
        });

        test('works with single element arrays', function (): void {
            // Arrange
            $combine = fn (int $a): callable => fn (int $b): callable => fn (int $c): int => $a + $b - $c;
            $liftedCombine = liftA3($combine);
            $as = [10];
            $bs = [5];
            $cs = [3];

            // Act
            $result = $liftedCombine($as, $bs, $cs);

            // Assert
            expect($result)->toBe([12]);
        });

        test('demonstrates three-way Cartesian product', function (): void {
            // Arrange
            $triple = fn (mixed $a): callable => fn (mixed $b): callable => fn (mixed $c): array => [$a, $b, $c];
            $liftedTriple = liftA3($triple);
            $as = [1, 2];
            $bs = ['x', 'y'];
            $cs = [true, false];

            // Act
            $result = $liftedTriple($as, $bs, $cs);

            // Assert
            expect($result)->toBe([
                [1, 'x', true], [1, 'x', false],
                [1, 'y', true], [1, 'y', false],
                [2, 'x', true], [2, 'x', false],
                [2, 'y', true], [2, 'y', false],
            ]);
        });

        test('lifts function with different operations', function (): void {
            // Arrange
            $formula = fn (int $a): callable => fn (int $b): callable => fn (int $c): int => ($a + $b) * $c;
            $liftedFormula = liftA3($formula);
            $as = [1, 2];
            $bs = [3, 4];
            $cs = [2];

            // Act
            $result = $liftedFormula($as, $bs, $cs);

            // Assert
            expect($result)->toBe([8, 10, 10, 12]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: liftA3() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns empty array when first array is empty', function (): void {
            // Arrange
            $sum3 = fn (int $a): callable => fn (int $b): callable => fn (int $c): int => $a + $b + $c;
            $liftedSum = liftA3($sum3);
            $as = [];
            $bs = [1, 2];
            $cs = [10, 20];

            // Act
            $result = $liftedSum($as, $bs, $cs);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns empty array when second array is empty', function (): void {
            // Arrange
            $sum3 = fn (int $a): callable => fn (int $b): callable => fn (int $c): int => $a + $b + $c;
            $liftedSum = liftA3($sum3);
            $as = [1, 2];
            $bs = [];
            $cs = [10, 20];

            // Act
            $result = $liftedSum($as, $bs, $cs);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns empty array when third array is empty', function (): void {
            // Arrange
            $sum3 = fn (int $a): callable => fn (int $b): callable => fn (int $c): int => $a + $b + $c;
            $liftedSum = liftA3($sum3);
            $as = [1, 2];
            $bs = [10, 20];
            $cs = [];

            // Act
            $result = $liftedSum($as, $bs, $cs);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns empty array when all arrays are empty', function (): void {
            // Arrange
            $sum3 = fn (int $a): callable => fn (int $b): callable => fn (int $c): int => $a + $b + $c;
            $liftedSum = liftA3($sum3);
            $as = [];
            $bs = [];
            $cs = [];

            // Act
            $result = $liftedSum($as, $bs, $cs);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles null values', function (): void {
            // Arrange
            $coalesce3 = fn (?int $a): callable => fn (?int $b): callable => fn (?int $c): int => $a ?? $b ?? $c ?? 0;
            $liftedCoalesce = liftA3($coalesce3);
            $as = [null, 1];
            $bs = [2];
            $cs = [null];

            // Act
            $result = $liftedCoalesce($as, $bs, $cs);

            // Assert
            expect($result)->toBe([2, 1]);
        });

        test('handles false values', function (): void {
            // Arrange
            $and3 = fn (bool $a): callable => fn (bool $b): callable => fn (bool $c): bool => $a && $b && $c;
            $liftedAnd = liftA3($and3);
            $as = [true, false];
            $bs = [true];
            $cs = [true, false];

            // Act
            $result = $liftedAnd($as, $bs, $cs);

            // Assert
            expect($result)->toBe([true, false, false, false]);
        });

        test('handles zero values', function (): void {
            // Arrange
            $multiply3 = fn (int $a): callable => fn (int $b): callable => fn (int $c): int => $a * $b * $c;
            $liftedMultiply = liftA3($multiply3);
            $as = [0, 1];
            $bs = [2];
            $cs = [3];

            // Act
            $result = $liftedMultiply($as, $bs, $cs);

            // Assert
            expect($result)->toBe([0, 6]);
        });

        test('handles empty strings', function (): void {
            // Arrange
            $concat3 = fn (string $a): callable => fn (string $b): callable => fn (string $c): string => $a.'-'.$b.'-'.$c;
            $liftedConcat = liftA3($concat3);
            $as = ['', 'a'];
            $bs = ['b'];
            $cs = [''];

            // Act
            $result = $liftedConcat($as, $bs, $cs);

            // Assert
            expect($result)->toBe(['-b-', 'a-b-']);
        });

        test('can be reused with different inputs', function (): void {
            // Arrange
            $sum3 = fn (int $a): callable => fn (int $b): callable => fn (int $c): int => $a + $b + $c;
            $liftedSum = liftA3($sum3);

            // Act
            $result1 = $liftedSum([1], [10], [100]);
            $result2 = $liftedSum([2, 3], [20], [200]);

            // Assert
            expect($result1)->toBe([111]);
            expect($result2)->toBe([222, 223]);
        });

        test('demonstrates order of application', function (): void {
            // Arrange
            $format3 = fn (string $a): callable => fn (string $b): callable => fn (string $c): string => sprintf('(%s,%s,%s)', $a, $b, $c);
            $liftedFormat = liftA3($format3);
            $as = ['a', 'b'];
            $bs = ['1'];
            $cs = ['x', 'y'];

            // Act
            $result = $liftedFormat($as, $bs, $cs);

            // Assert
            expect($result)->toBe(['(a,1,x)', '(a,1,y)', '(b,1,x)', '(b,1,y)']);
        });

        test('works with larger arrays', function (): void {
            // Arrange
            $sum3 = fn (int $a): callable => fn (int $b): callable => fn (int $c): int => $a + $b + $c;
            $liftedSum = liftA3($sum3);
            $as = [1, 2, 3];
            $bs = [10, 20];
            $cs = [100];

            // Act
            $result = $liftedSum($as, $bs, $cs);

            // Assert
            expect($result)->toHaveCount(6); // 3 * 2 * 1
            expect($result)->toContain(111); // 1+10+100
            expect($result)->toContain(123); // 3+20+100
        });

        test('lifts function returning arrays', function (): void {
            // Arrange
            $makeArray = fn (int $a): callable => fn (int $b): callable => fn (int $c): array => [$a, $b, $c];
            $liftedMakeArray = liftA3($makeArray);
            $as = [1];
            $bs = [2];
            $cs = [3, 4];

            // Act
            $result = $liftedMakeArray($as, $bs, $cs);

            // Assert
            expect($result)->toBe([[1, 2, 3], [1, 2, 4]]);
        });

        test('lifts function returning objects', function (): void {
            // Arrange
            $makeObj = fn (int $a): callable => fn (int $b): callable => fn (int $c): object => (object) ['a' => $a, 'b' => $b, 'c' => $c];
            $liftedMakeObj = liftA3($makeObj);
            $as = [1, 2];
            $bs = [10];
            $cs = [100];

            // Act
            $result = $liftedMakeObj($as, $bs, $cs);

            // Assert
            expect($result)->toHaveCount(2);
            expect($result[0]->a)->toBe(1);
            expect($result[0]->b)->toBe(10);
            expect($result[0]->c)->toBe(100);
            expect($result[1]->a)->toBe(2);
        });

        test('works with min function across three values', function (): void {
            // Arrange
            $min3 = fn (int $a): callable => fn (int $b): callable => fn (int $c): int => min($a, $b, $c);
            $liftedMin = liftA3($min3);
            $as = [5, 10];
            $bs = [3, 8];
            $cs = [7];

            // Act
            $result = $liftedMin($as, $bs, $cs);

            // Assert
            expect($result)->toBe([3, 5, 3, 7]);
        });

        test('works with max function across three values', function (): void {
            // Arrange
            $max3 = fn (int $a): callable => fn (int $b): callable => fn (int $c): int => max($a, $b, $c);
            $liftedMax = liftA3($max3);
            $as = [5, 10];
            $bs = [3, 8];
            $cs = [7];

            // Act
            $result = $liftedMax($as, $bs, $cs);

            // Assert
            expect($result)->toBe([7, 8, 10, 10]);
        });

        test('useful for combining three validation results', function (): void {
            // Arrange
            $allValid = fn (bool $a): callable => fn (bool $b): callable => fn (bool $c): bool => $a && $b && $c;
            $liftedAllValid = liftA3($allValid);
            $validation1 = [true];
            $validation2 = [true, false];
            $validation3 = [true];

            // Act
            $result = $liftedAllValid($validation1, $validation2, $validation3);

            // Assert
            expect($result)->toBe([true, false]);
        });

        test('demonstrates triple Cartesian product size', function (): void {
            // Arrange
            $tuple = fn (int $a): callable => fn (int $b): callable => fn (int $c): array => [$a, $b, $c];
            $liftedTuple = liftA3($tuple);
            $as = [1, 2, 3];
            $bs = [10, 20];
            $cs = [100, 200, 300];

            // Act
            $result = $liftedTuple($as, $bs, $cs);

            // Assert
            expect($result)->toHaveCount(18); // 3 * 2 * 3 = 18
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
