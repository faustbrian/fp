<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_keys;
use function Cline\fp\difference;
use function describe;
use function expect;
use function test;

describe('difference', function (): void {
    describe('Happy Paths', function (): void {
        test('returns values in first array not in second', function (): void {
            // Arrange
            $array = [1, 2, 3, 4, 5];
            $other = [3, 4, 5, 6, 7];

            // Act
            $result = difference($array, $other);

            // Assert
            expect($result)->toBe([1, 2]);
        });

        test('returns values in first array not in multiple other arrays', function (): void {
            // Arrange
            $array = [1, 2, 3, 4, 5, 6];
            $other1 = [2, 3];
            $other2 = [4, 5];

            // Act
            $result = difference($array, $other1, $other2);

            // Assert
            expect($result)->toBe([1, 6]);
        });

        test('returns string differences correctly', function (): void {
            // Arrange
            $array = ['apple', 'banana', 'cherry', 'date'];
            $other = ['banana', 'date'];

            // Act
            $result = difference($array, $other);

            // Assert
            expect($result)->toBe(['apple', 'cherry']);
        });

        test('returns values reindexed from zero', function (): void {
            // Arrange
            $array = [10 => 'a', 20 => 'b', 30 => 'c'];
            $other = [40 => 'b'];

            // Act
            $result = difference($array, $other);

            // Assert
            expect($result)->toBe([0 => 'a', 1 => 'c']);
            expect(array_keys($result))->toBe([0, 1]);
        });
    });

    describe('Sad Paths', function (): void {
        test('returns empty array when all values present in other arrays', function (): void {
            // Arrange
            $array = [1, 2, 3];
            $other = [1, 2, 3, 4, 5];

            // Act
            $result = difference($array, $other);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns empty array when all values present across multiple others', function (): void {
            // Arrange
            $array = [1, 2, 3, 4];
            $other1 = [1, 2];
            $other2 = [3, 4];

            // Act
            $result = difference($array, $other1, $other2);

            // Assert
            expect($result)->toBe([]);
        });
    });

    describe('Edge Cases', function (): void {
        test('returns empty array when first array is empty', function (): void {
            // Arrange
            $array = [];
            $other = [1, 2, 3];

            // Act
            $result = difference($array, $other);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns first array when no other arrays provided', function (): void {
            // Arrange
            $array = [1, 2, 3, 4, 5];

            // Act
            $result = difference($array);

            // Assert
            expect($result)->toBe([1, 2, 3, 4, 5]);
        });

        test('returns first array when other arrays are empty', function (): void {
            // Arrange
            $array = [1, 2, 3];
            $other1 = [];
            $other2 = [];

            // Act
            $result = difference($array, $other1, $other2);

            // Assert
            expect($result)->toBe([1, 2, 3]);
        });

        test('handles duplicate values in first array', function (): void {
            // Arrange
            $array = [1, 2, 2, 3, 3, 3, 4];
            $other = [2, 3];

            // Act
            $result = difference($array, $other);

            // Assert
            expect($result)->toBe([1, 4]);
        });

        test('handles mixed types correctly', function (): void {
            // Arrange
            $array = [1, '1', 2, '2', 3];
            $other = [1, 2];

            // Act
            $result = difference($array, $other);

            // Assert - array_diff removes by loose comparison (1 == '1', 2 == '2')
            expect($result)->toBe([3]);
        });

        test('handles associative arrays by value comparison', function (): void {
            // Arrange
            $array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
            $other = ['x' => 2, 'y' => 4];

            // Act
            $result = difference($array, $other);

            // Assert
            expect($result)->toBe([0 => 1, 1 => 3]);
        });

        test('handles null and boolean values', function (): void {
            // Arrange
            $array = [null, true, false, 0, '', 1];
            $other = [false, ''];

            // Act
            $result = difference($array, $other);

            // Assert - array_diff uses loose comparison (false == null == 0 == '')
            expect($result)->toBe([true, 0, 1]);
        });
    });
});
