<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_keys;
use function Cline\fp\union;
use function describe;
use function expect;
use function test;

describe('union', function (): void {
    describe('Happy Paths', function (): void {
        test('combines two arrays with deduplication', function (): void {
            // Arrange
            $array1 = [1, 2, 3];
            $array2 = [3, 4, 5];

            // Act
            $result = union($array1, $array2);

            // Assert
            expect($result)->toBe([1, 2, 3, 4, 5]);
        });

        test('combines multiple arrays with deduplication', function (): void {
            // Arrange
            $array1 = [1, 2, 3];
            $array2 = [3, 4, 5];
            $array3 = [5, 6, 7];

            // Act
            $result = union($array1, $array2, $array3);

            // Assert
            expect($result)->toBe([1, 2, 3, 4, 5, 6, 7]);
        });

        test('combines string arrays with deduplication', function (): void {
            // Arrange
            $array1 = ['apple', 'banana'];
            $array2 = ['banana', 'cherry'];
            $array3 = ['cherry', 'date'];

            // Act
            $result = union($array1, $array2, $array3);

            // Assert
            expect($result)->toBe(['apple', 'banana', 'cherry', 'date']);
        });

        test('returns values reindexed from zero', function (): void {
            // Arrange
            $array1 = [10 => 'a', 20 => 'b'];
            $array2 = [30 => 'c', 40 => 'd'];

            // Act
            $result = union($array1, $array2);

            // Assert
            expect($result)->toBe([0 => 'a', 1 => 'b', 2 => 'c', 3 => 'd']);
            expect(array_keys($result))->toBe([0, 1, 2, 3]);
        });
    });

    describe('Edge Cases', function (): void {
        test('returns empty array when no arrays provided', function (): void {
            // Arrange & Act
            $result = union();

            // Assert
            expect($result)->toBe([]);
        });

        test('returns single array when only one provided', function (): void {
            // Arrange
            $array = [1, 2, 3, 4, 5];

            // Act
            $result = union($array);

            // Assert
            expect($result)->toBe([1, 2, 3, 4, 5]);
        });

        test('returns empty array when all input arrays are empty', function (): void {
            // Arrange
            $array1 = [];
            $array2 = [];
            $array3 = [];

            // Act
            $result = union($array1, $array2, $array3);

            // Assert
            expect($result)->toBe([]);
        });

        test('deduplicates when all arrays contain same values', function (): void {
            // Arrange
            $array1 = [1, 2, 3];
            $array2 = [1, 2, 3];
            $array3 = [1, 2, 3];

            // Act
            $result = union($array1, $array2, $array3);

            // Assert
            expect($result)->toBe([1, 2, 3]);
        });

        test('handles duplicate values within single array', function (): void {
            // Arrange
            $array1 = [1, 2, 2, 3, 3, 3];
            $array2 = [4, 4, 5, 5, 5];

            // Act
            $result = union($array1, $array2);

            // Assert
            expect($result)->toBe([1, 2, 3, 4, 5]);
        });

        test('handles mixed types correctly', function (): void {
            // Arrange
            $array1 = [1, '1', 2];
            $array2 = ['1', '2', 3];

            // Act
            $result = union($array1, $array2);

            // Assert - array_unique uses loose comparison (1 == '1', 2 == '2')
            expect($result)->toBe([1, 2, 3]);
        });

        test('handles associative arrays by value combination', function (): void {
            // Arrange
            $array1 = ['a' => 1, 'b' => 2];
            $array2 = ['c' => 3, 'd' => 4];
            $array3 = ['e' => 2, 'f' => 5];

            // Act
            $result = union($array1, $array2, $array3);

            // Assert
            expect($result)->toBe([0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5]);
        });

        test('handles null and boolean values', function (): void {
            // Arrange
            $array1 = [null, true, false];
            $array2 = [false, 0, ''];
            $array3 = [null, 1];

            // Act
            $result = union($array1, $array2, $array3);

            // Assert - array_unique uses loose comparison (null == false == 0 == '')
            expect($result)->toBe([null, true, 0]);
        });

        test('combines single element arrays', function (): void {
            // Arrange
            $array1 = [1];
            $array2 = [2];
            $array3 = [3];

            // Act
            $result = union($array1, $array2, $array3);

            // Assert
            expect($result)->toBe([1, 2, 3]);
        });

        test('handles float values correctly', function (): void {
            // Arrange
            $array1 = [1.5, 2.5, 3.5];
            $array2 = [2.5, 3.5, 4.5];

            // Act
            $result = union($array1, $array2);

            // Assert
            expect($result)->toBe([1.5, 2.5, 3.5, 4.5]);
        });
    });
});
