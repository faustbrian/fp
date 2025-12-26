<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_keys;
use function Cline\fp\intersection;
use function describe;
use function expect;
use function test;

describe('intersection', function (): void {
    describe('Happy Paths', function (): void {
        test('returns common elements from two arrays', function (): void {
            // Arrange
            $array1 = [1, 2, 3, 4, 5];
            $array2 = [3, 4, 5, 6, 7];

            // Act
            $result = intersection($array1, $array2);

            // Assert
            expect($result)->toBe([3, 4, 5]);
        });

        test('returns common elements from three arrays', function (): void {
            // Arrange
            $array1 = [1, 2, 3, 4, 5];
            $array2 = [2, 3, 4, 5, 6];
            $array3 = [3, 4, 5, 6, 7];

            // Act
            $result = intersection($array1, $array2, $array3);

            // Assert
            expect($result)->toBe([3, 4, 5]);
        });

        test('returns common string elements', function (): void {
            // Arrange
            $array1 = ['apple', 'banana', 'cherry'];
            $array2 = ['banana', 'cherry', 'date'];

            // Act
            $result = intersection($array1, $array2);

            // Assert
            expect($result)->toBe(['banana', 'cherry']);
        });

        test('returns values reindexed from zero', function (): void {
            // Arrange
            $array1 = [10 => 'a', 20 => 'b', 30 => 'c'];
            $array2 = [40 => 'b', 50 => 'c', 60 => 'd'];

            // Act
            $result = intersection($array1, $array2);

            // Assert
            expect($result)->toBe([0 => 'b', 1 => 'c']);
            expect(array_keys($result))->toBe([0, 1]);
        });
    });

    describe('Sad Paths', function (): void {
        test('returns empty array when no common elements exist', function (): void {
            // Arrange
            $array1 = [1, 2, 3];
            $array2 = [4, 5, 6];

            // Act
            $result = intersection($array1, $array2);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns empty array when no common elements in multiple arrays', function (): void {
            // Arrange
            $array1 = [1, 2, 3];
            $array2 = [2, 3, 4];
            $array3 = [5, 6, 7];

            // Act
            $result = intersection($array1, $array2, $array3);

            // Assert
            expect($result)->toBe([]);
        });
    });

    describe('Edge Cases', function (): void {
        test('returns empty array when no arrays provided', function (): void {
            // Arrange & Act
            $result = intersection();

            // Assert
            expect($result)->toBe([]);
        });

        test('returns single array values when only one array provided', function (): void {
            // Arrange
            $array = [1, 2, 3, 4, 5];

            // Act
            $result = intersection($array);

            // Assert
            expect($result)->toBe([1, 2, 3, 4, 5]);
        });

        test('returns empty array when one input array is empty', function (): void {
            // Arrange
            $array1 = [1, 2, 3];
            $array2 = [];

            // Act
            $result = intersection($array1, $array2);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles duplicate values in input arrays', function (): void {
            // Arrange
            $array1 = [1, 2, 2, 3, 3, 3];
            $array2 = [2, 2, 3, 4, 4];

            // Act
            $result = intersection($array1, $array2);

            // Assert - array_intersect preserves duplicates from first array
            expect($result)->toBe([2, 2, 3, 3, 3]);
        });

        test('handles mixed types correctly', function (): void {
            // Arrange
            $array1 = [1, '1', 2, '2'];
            $array2 = ['1', '2', '3'];

            // Act
            $result = intersection($array1, $array2);

            // Assert - array_intersect does NOT do loose comparison, keeps types intact
            expect($result)->toBe([1, '1', 2, '2']);
        });

        test('handles associative arrays by value comparison', function (): void {
            // Arrange
            $array1 = ['a' => 1, 'b' => 2, 'c' => 3];
            $array2 = ['x' => 2, 'y' => 3, 'z' => 4];

            // Act
            $result = intersection($array1, $array2);

            // Assert
            expect($result)->toBe([0 => 2, 1 => 3]);
        });

        test('handles null and boolean values', function (): void {
            // Arrange
            $array1 = [null, true, false, 0, ''];
            $array2 = [null, false, ''];

            // Act
            $result = intersection($array1, $array2);

            // Assert
            expect($result)->toBe([null, false, '']);
        });
    });
});
