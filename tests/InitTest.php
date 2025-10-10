<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_keys;
use function Cline\fp\init;
use function describe;
use function expect;
use function range;
use function test;

describe('init()', function (): void {
    describe('Happy Paths', function (): void {
        test('returns all elements except last from numeric array', function (): void {
            // Arrange
            $input = [1, 2, 3, 4, 5];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe([1, 2, 3, 4]);
        });

        test('returns all elements except last from associative array', function (): void {
            // Arrange
            $input = ['a' => 1, 'b' => 2, 'c' => 3];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe([1, 2]);
        });

        test('returns all elements except last from string array', function (): void {
            // Arrange
            $input = ['hello', 'world', 'foo', 'bar'];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe(['hello', 'world', 'foo']);
        });

        test('works with large arrays efficiently', function (): void {
            // Arrange
            $input = range(1, 1_000);

            // Act
            $result = init($input);

            // Assert
            expect($result)->toHaveCount(999);
            expect($result[0])->toBe(1);
            expect($result[998])->toBe(999);
        });

        test('handles mixed type arrays', function (): void {
            // Arrange
            $input = [1, 'two', 3.0, false];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe([1, 'two', 3.0]);
        });

        test('does not preserve string keys', function (): void {
            // Arrange
            $input = ['first' => 1, 'second' => 2, 'third' => 3];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe([1, 2]);
            expect(array_keys($result))->toBe([0, 1]);
        });

        test('handles array with null values', function (): void {
            // Arrange
            $input = [1, null, 3, null];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe([1, null, 3]);
        });

        test('handles array with false values', function (): void {
            // Arrange
            $input = [true, false, true, false];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe([true, false, true]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: init() is type-safe at the PHP level and expects array
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns empty array for empty input', function (): void {
            // Arrange
            $input = [];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns empty array for single element array', function (): void {
            // Arrange
            $input = [42];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles array with two elements', function (): void {
            // Arrange
            $input = ['first', 'second'];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe(['first']);
        });

        test('handles array with objects', function (): void {
            // Arrange
            $obj1 = (object) ['id' => 1];
            $obj2 = (object) ['id' => 2];
            $obj3 = (object) ['id' => 3];
            $input = [$obj1, $obj2, $obj3];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toHaveCount(2);
            expect($result[0])->toBe($obj1);
            expect($result[1])->toBe($obj2);
        });

        test('handles array with nested arrays', function (): void {
            // Arrange
            $input = [[1, 2], [3, 4], [5, 6]];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe([[1, 2], [3, 4]]);
        });

        test('handles array with zero values', function (): void {
            // Arrange
            $input = [0, 0, 0, 0];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe([0, 0, 0]);
        });

        test('handles array with empty strings', function (): void {
            // Arrange
            $input = ['', '', ''];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe(['', '']);
        });

        test('handles array with duplicate values', function (): void {
            // Arrange
            $input = [1, 1, 1, 1, 1];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe([1, 1, 1, 1]);
        });

        test('does not mutate original array', function (): void {
            // Arrange
            $input = [1, 2, 3, 4];
            $original = $input;

            // Act
            $result = init($input);

            // Assert
            expect($input)->toBe($original);
            expect($result)->not->toBe($input);
        });

        test('reindexes numeric keys', function (): void {
            // Arrange
            $input = [10 => 'a', 20 => 'b', 30 => 'c'];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe(['a', 'b']);
            expect(array_keys($result))->toBe([0, 1]);
        });

        test('handles array starting with negative keys', function (): void {
            // Arrange
            $input = [-2 => 'a', -1 => 'b', 0 => 'c'];

            // Act
            $result = init($input);

            // Assert
            expect($result)->toBe(['a', 'b']);
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
