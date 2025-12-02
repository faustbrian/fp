<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_keys;
use function Cline\fp\concat;
use function describe;
use function expect;
use function range;
use function test;

describe('concat()', function (): void {
    describe('Happy Paths', function (): void {
        test('concatenates two numeric arrays', function (): void {
            // Arrange & Act
            $result = concat([1, 2], [3, 4]);

            // Assert
            expect($result)->toBe([1, 2, 3, 4]);
        });

        test('concatenates multiple arrays', function (): void {
            // Arrange & Act
            $result = concat([1], [2, 3], [4, 5, 6]);

            // Assert
            expect($result)->toBe([1, 2, 3, 4, 5, 6]);
        });

        test('concatenates string arrays', function (): void {
            // Arrange & Act
            $result = concat(['hello', 'world'], ['foo', 'bar']);

            // Assert
            expect($result)->toBe(['hello', 'world', 'foo', 'bar']);
        });

        test('preserves string keys', function (): void {
            // Arrange & Act
            $result = concat(['a' => 1], ['b' => 2], ['c' => 3]);

            // Assert
            expect($result)->toBe(['a' => 1, 'b' => 2, 'c' => 3]);
        });

        test('reindexes numeric keys sequentially', function (): void {
            // Arrange & Act
            $result = concat([0 => 'a', 1 => 'b'], [0 => 'c', 1 => 'd']);

            // Assert
            expect($result)->toBe(['a', 'b', 'c', 'd']);
            expect(array_keys($result))->toBe([0, 1, 2, 3]);
        });

        test('later arrays overwrite earlier for duplicate string keys', function (): void {
            // Arrange & Act
            $result = concat(['a' => 1, 'b' => 2], ['b' => 3, 'c' => 4]);

            // Assert
            expect($result)->toBe(['a' => 1, 'b' => 3, 'c' => 4]);
        });

        test('handles mixed type arrays', function (): void {
            // Arrange & Act
            $result = concat([1, 'two'], [3.0, false], [null]);

            // Assert
            expect($result)->toBe([1, 'two', 3.0, false, null]);
        });

        test('concatenates arrays with objects', function (): void {
            // Arrange
            $obj1 = (object) ['id' => 1];
            $obj2 = (object) ['id' => 2];

            // Act
            $result = concat([$obj1], [$obj2]);

            // Assert
            expect($result)->toBe([$obj1, $obj2]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: concat() is type-safe at the PHP level and expects arrays
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('concatenates with empty array at start', function (): void {
            // Arrange & Act
            $result = concat([], [1, 2, 3]);

            // Assert
            expect($result)->toBe([1, 2, 3]);
        });

        test('concatenates with empty array at end', function (): void {
            // Arrange & Act
            $result = concat([1, 2, 3], []);

            // Assert
            expect($result)->toBe([1, 2, 3]);
        });

        test('concatenates with empty arrays in middle', function (): void {
            // Arrange & Act
            $result = concat([1], [], [2], [], [3]);

            // Assert
            expect($result)->toBe([1, 2, 3]);
        });

        test('concatenates all empty arrays', function (): void {
            // Arrange & Act
            $result = concat([], [], []);

            // Assert
            expect($result)->toBe([]);
        });

        test('concatenates single array', function (): void {
            // Arrange & Act
            $result = concat([1, 2, 3]);

            // Assert
            expect($result)->toBe([1, 2, 3]);
        });

        test('concatenates arrays with null values', function (): void {
            // Arrange & Act
            $result = concat([1, null], [null, 2]);

            // Assert
            expect($result)->toBe([1, null, null, 2]);
        });

        test('concatenates arrays with false values', function (): void {
            // Arrange & Act
            $result = concat([true, false], [false, true]);

            // Assert
            expect($result)->toBe([true, false, false, true]);
        });

        test('concatenates arrays with zero values', function (): void {
            // Arrange & Act
            $result = concat([0, 1], [2, 0]);

            // Assert
            expect($result)->toBe([0, 1, 2, 0]);
        });

        test('concatenates arrays with empty strings', function (): void {
            // Arrange & Act
            $result = concat(['', 'a'], ['b', '']);

            // Assert
            expect($result)->toBe(['', 'a', 'b', '']);
        });

        test('concatenates large arrays efficiently', function (): void {
            // Arrange
            $arr1 = range(1, 500);
            $arr2 = range(501, 1_000);

            // Act
            $result = concat($arr1, $arr2);

            // Assert
            expect($result)->toHaveCount(1_000);
            expect($result[0])->toBe(1);
            expect($result[999])->toBe(1_000);
        });

        test('concatenates arrays with nested arrays', function (): void {
            // Arrange & Act
            $result = concat([[1, 2]], [[3, 4]], [[5, 6]]);

            // Assert
            expect($result)->toBe([[1, 2], [3, 4], [5, 6]]);
        });

        test('handles arrays with mixed keys', function (): void {
            // Arrange & Act
            $result = concat([0 => 'a', 'key' => 'b'], [1 => 'c', 'other' => 'd']);

            // Assert
            expect($result)->toBe([0 => 'a', 'key' => 'b', 1 => 'c', 'other' => 'd']);
        });

        test('does not mutate input arrays', function (): void {
            // Arrange
            $arr1 = [1, 2];
            $arr2 = [3, 4];
            $original1 = $arr1;
            $original2 = $arr2;

            // Act
            $result = concat($arr1, $arr2);

            // Assert
            expect($arr1)->toBe($original1);
            expect($arr2)->toBe($original2);
        });

        test('concatenates arrays with duplicate values', function (): void {
            // Arrange & Act
            $result = concat([1, 2, 1], [2, 3, 2]);

            // Assert
            expect($result)->toBe([1, 2, 1, 2, 3, 2]);
        });

        test('handles array with special character keys', function (): void {
            // Arrange & Act
            $result = concat(['key-1' => 'a'], ['key.2' => 'b'], ['key_3' => 'c']);

            // Assert
            expect($result)->toBe(['key-1' => 'a', 'key.2' => 'b', 'key_3' => 'c']);
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
