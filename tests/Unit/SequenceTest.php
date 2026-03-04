<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\sequence;
use function describe;
use function expect;
use function test;

describe('sequence()', function (): void {
    describe('Happy Paths', function (): void {
        test('transposes 2D array', function (): void {
            // Arrange
            $input = [[1, 2], [3, 4], [5, 6]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([[1, 3, 5], [2, 4, 6]]);
        });

        test('transposes single row into columns', function (): void {
            // Arrange
            $input = [[1, 2, 3]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([[1], [2], [3]]);
        });

        test('inverts structure of nested arrays', function (): void {
            // Arrange
            $input = [[1, 2, 3], [4, 5, 6]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([[1, 4], [2, 5], [3, 6]]);
        });

        test('transposes square matrix', function (): void {
            // Arrange
            $input = [[1, 2, 3], [4, 5, 6], [7, 8, 9]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([[1, 4, 7], [2, 5, 8], [3, 6, 9]]);
        });

        test('works with string values', function (): void {
            // Arrange
            $input = [['a', 'b'], ['c', 'd'], ['e', 'f']];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([['a', 'c', 'e'], ['b', 'd', 'f']]);
        });

        test('transposes three columns', function (): void {
            // Arrange
            $input = [[1, 2, 3], [4, 5, 6]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([[1, 4], [2, 5], [3, 6]]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: sequence() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns empty array for empty input', function (): void {
            // Arrange
            $input = [];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles arrays with empty sub-arrays', function (): void {
            // Arrange
            $input = [[], [], []];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles single empty array', function (): void {
            // Arrange
            $input = [[]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles single column', function (): void {
            // Arrange
            $input = [[1], [2], [3]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([[1, 2, 3]]);
        });

        test('handles ragged arrays by taking maximum length', function (): void {
            // Arrange
            $input = [[1, 2, 3], [4, 5], [6]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([[1, 4, 6], [2, 5], [3]]);
        });

        test('handles ragged arrays with different patterns', function (): void {
            // Arrange
            $input = [[1], [2, 3], [4, 5, 6]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([[1, 2, 4], [3, 5], [6]]);
        });

        test('handles null values', function (): void {
            // Arrange
            $input = [[1, null], [3, 4], [5, null]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([[1, 3, 5], [null, 4, null]]);
        });

        test('handles false values', function (): void {
            // Arrange
            $input = [[true, false], [false, true]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([[true, false], [false, true]]);
        });

        test('handles zero values', function (): void {
            // Arrange
            $input = [[0, 1], [2, 0], [0, 3]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([[0, 2, 0], [1, 0, 3]]);
        });

        test('handles empty strings', function (): void {
            // Arrange
            $input = [['', 'a'], ['b', ''], ['', 'c']];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([['', 'b', ''], ['a', '', 'c']]);
        });

        test('handles mixed types', function (): void {
            // Arrange
            $input = [[1, 'a'], [2, 'b'], [3, 'c']];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([[1, 2, 3], ['a', 'b', 'c']]);
        });

        test('transposes twice returns similar structure', function (): void {
            // Arrange
            $input = [[1, 2, 3], [4, 5, 6]];

            // Act
            $once = sequence($input);
            $twice = sequence($once);

            // Assert
            expect($twice)->toBe($input);
        });

        test('works with single value arrays', function (): void {
            // Arrange
            $input = [[1], [2]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([[1, 2]]);
        });

        test('handles wide arrays', function (): void {
            // Arrange
            $input = [[1, 2, 3, 4, 5], [6, 7, 8, 9, 10]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([[1, 6], [2, 7], [3, 8], [4, 9], [5, 10]]);
        });

        test('handles tall arrays', function (): void {
            // Arrange
            $input = [[1, 2], [3, 4], [5, 6], [7, 8], [9, 10]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([[1, 3, 5, 7, 9], [2, 4, 6, 8, 10]]);
        });

        test('useful for applicative pattern', function (): void {
            // Arrange - converting [[a]] to [a] pattern
            $wrapped = [[1], [2], [3]];

            // Act
            $result = sequence($wrapped);

            // Assert
            expect($result)->toBe([[1, 2, 3]]);
        });

        test('inverts nesting for validation results', function (): void {
            // Arrange - each validator returns array of results
            $validationResults = [[true, true], [true, false], [false, true]];

            // Act
            $result = sequence($validationResults);

            // Assert
            expect($result)->toBe([[true, true, false], [true, false, true]]);
        });

        test('handles arrays with objects', function (): void {
            // Arrange
            $obj1 = (object) ['id' => 1];
            $obj2 = (object) ['id' => 2];
            $obj3 = (object) ['id' => 3];
            $obj4 = (object) ['id' => 4];
            $input = [[$obj1, $obj2], [$obj3, $obj4]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toHaveCount(2);
            expect($result[0])->toBe([$obj1, $obj3]);
            expect($result[1])->toBe([$obj2, $obj4]);
        });

        test('preserves array index order', function (): void {
            // Arrange
            $input = [[1, 2, 3], [10, 20, 30], [100, 200, 300]];

            // Act
            $result = sequence($input);

            // Assert
            expect($result[0])->toBe([1, 10, 100]);
            expect($result[1])->toBe([2, 20, 200]);
            expect($result[2])->toBe([3, 30, 300]);
        });

        test('works with numeric string keys', function (): void {
            // Arrange
            $input = [['0' => 'a', '1' => 'b'], ['0' => 'c', '1' => 'd']];

            // Act
            $result = sequence($input);

            // Assert
            expect($result)->toBe([['a', 'c'], ['b', 'd']]);
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
