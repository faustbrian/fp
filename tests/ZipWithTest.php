<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_sum;
use function Cline\fp\zipWith;
use function count;
use function describe;
use function expect;
use function sprintf;
use function test;

describe('zipWith', function (): void {
    describe('Happy Paths', function (): void {
        test('combines two arrays using addition function', function (): void {
            // Arrange
            $array1 = [1, 2, 3];
            $array2 = [10, 20, 30];
            $add = fn ($a, $b): float|int|array => $a + $b;

            // Act
            $result = zipWith($add)($array1, $array2);

            // Assert
            expect($result)->toBe([11, 22, 33]);
        });

        test('combines two arrays using string concatenation', function (): void {
            // Arrange
            $array1 = ['Hello', 'Good', 'Nice'];
            $array2 = ['World', 'Morning', 'Day'];
            $concat = fn ($a, $b): string => sprintf('%s %s', $a, $b);

            // Act
            $result = zipWith($concat)($array1, $array2);

            // Assert
            expect($result)->toBe([
                'Hello World',
                'Good Morning',
                'Nice Day',
            ]);
        });

        test('combines three arrays with custom combiner function', function (): void {
            // Arrange
            $array1 = [1, 2, 3];
            $array2 = [10, 20, 30];
            $array3 = [100, 200, 300];
            $combiner = fn ($a, $b, $c): float|int|array => $a + $b + $c;

            // Act
            $result = zipWith($combiner)($array1, $array2, $array3);

            // Assert
            expect($result)->toBe([111, 222, 333]);
        });

        test('combines arrays using multiplication function', function (): void {
            // Arrange
            $array1 = [2, 3, 4];
            $array2 = [5, 6, 7];
            $multiply = fn ($a, $b): int|float => $a * $b;

            // Act
            $result = zipWith($multiply)($array1, $array2);

            // Assert
            expect($result)->toBe([10, 18, 28]);
        });
    });

    describe('Sad Paths', function (): void {
        test('stops at shortest array when arrays differ in length', function (): void {
            // Arrange
            $array1 = [1, 2, 3, 4, 5];
            $array2 = [10, 20];
            $array3 = [100, 200, 300, 400];
            $combiner = fn ($a, $b, $c): float|int|array => $a + $b + $c;

            // Act
            $result = zipWith($combiner)($array1, $array2, $array3);

            // Assert
            expect($result)->toBe([111, 222]);
        });

        test('returns empty array when called with no arrays', function (): void {
            // Arrange
            $combiner = fn ($a, $b): float|int|array => $a + $b;

            // Act
            $result = zipWith($combiner)();

            // Assert
            expect($result)->toBe([]);
        });

        test('returns empty array when any array is empty', function (): void {
            // Arrange
            $array1 = [1, 2, 3];
            $array2 = [];
            $array3 = [100, 200, 300];
            $combiner = fn ($a, $b, $c): float|int|array => $a + $b + $c;

            // Act
            $result = zipWith($combiner)($array1, $array2, $array3);

            // Assert
            expect($result)->toBe([]);
        });
    });

    describe('Edge Cases', function (): void {
        test('applies function to each element of single array', function (): void {
            // Arrange
            $array = [1, 2, 3];
            $double = fn ($x): int|float => $x * 2;

            // Act
            $result = zipWith($double)($array);

            // Assert
            expect($result)->toBe([2, 4, 6]);
        });

        test('preserves values and ignores keys from associative arrays', function (): void {
            // Arrange
            $array1 = ['one' => 1, 'two' => 2, 'three' => 3];
            $array2 = ['a' => 10, 'b' => 20, 'c' => 30];
            $add = fn ($a, $b): float|int|array => $a + $b;

            // Act
            $result = zipWith($add)($array1, $array2);

            // Assert
            expect($result)->toBe([11, 22, 33]);
        });

        test('works with numeric keys that are not sequential', function (): void {
            // Arrange
            $array1 = [10 => 'a', 20 => 'b', 30 => 'c'];
            $array2 = [100 => 'x', 200 => 'y', 300 => 'z'];
            $concat = fn ($a, $b): string => $a.$b;

            // Act
            $result = zipWith($concat)($array1, $array2);

            // Assert
            expect($result)->toBe(['ax', 'by', 'cz']);
        });

        test('handles complex combiner function with conditionals', function (): void {
            // Arrange
            $array1 = [1, 2, 3, 4];
            $array2 = [10, 20, 30, 40];
            $combiner = fn ($a, $b): float|int|array => $a > 2 ? $a * $b : $a + $b;

            // Act
            $result = zipWith($combiner)($array1, $array2);

            // Assert
            expect($result)->toBe([
                11,  // 1 + 10 (1 <= 2)
                22,  // 2 + 20 (2 <= 2)
                90,  // 3 * 30 (3 > 2)
                160, // 4 * 40 (4 > 2)
            ]);
        });

        test('works with combiner that returns different types', function (): void {
            // Arrange
            $array1 = [1, 2, 3];
            $array2 = ['a', 'b', 'c'];
            $combiner = fn ($num, $str): array => ['number' => $num, 'letter' => $str];

            // Act
            $result = zipWith($combiner)($array1, $array2);

            // Assert
            expect($result)->toBe([
                ['number' => 1, 'letter' => 'a'],
                ['number' => 2, 'letter' => 'b'],
                ['number' => 3, 'letter' => 'c'],
            ]);
        });

        test('handles single element arrays', function (): void {
            // Arrange
            $array1 = [5];
            $array2 = [10];
            $multiply = fn ($a, $b): int|float => $a * $b;

            // Act
            $result = zipWith($multiply)($array1, $array2);

            // Assert
            expect($result)->toBe([50]);
        });

        test('works with four arrays', function (): void {
            // Arrange
            $array1 = [1, 2];
            $array2 = [10, 20];
            $array3 = [100, 200];
            $array4 = [1_000, 2_000];
            $combiner = fn ($a, $b, $c, $d): float|int|array => $a + $b + $c + $d;

            // Act
            $result = zipWith($combiner)($array1, $array2, $array3, $array4);

            // Assert
            expect($result)->toBe([1_111, 2_222]);
        });

        test('combiner receives correct number of arguments', function (): void {
            // Arrange
            $array1 = [1, 2, 3];
            $array2 = [10, 20, 30];
            $array3 = [100, 200, 300];
            $receivedArgs = [];
            $combiner = function (...$args) use (&$receivedArgs): float|int {
                $receivedArgs[] = count($args);

                return array_sum($args);
            };

            // Act
            $result = zipWith($combiner)($array1, $array2, $array3);

            // Assert
            expect($result)->toBe([111, 222, 333]);
            expect($receivedArgs)->toBe([3, 3, 3]); // Each call received 3 arguments
        });
    });
});
