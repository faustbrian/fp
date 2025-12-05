<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\reverse;
use function describe;
use function expect;
use function range;
use function test;

describe('reverse()', function (): void {
    describe('Happy Paths', function (): void {
        test('reverses numeric array', function (): void {
            // Arrange
            $input = [1, 2, 3, 4, 5];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe([4 => 5, 3 => 4, 2 => 3, 1 => 2, 0 => 1]);
        });

        test('reverses string array', function (): void {
            // Arrange
            $input = ['hello', 'world', 'foo', 'bar'];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe([3 => 'bar', 2 => 'foo', 1 => 'world', 0 => 'hello']);
        });

        test('preserves associative array keys', function (): void {
            // Arrange
            $input = ['a' => 1, 'b' => 2, 'c' => 3];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe(['c' => 3, 'b' => 2, 'a' => 1]);
        });

        test('preserves numeric keys', function (): void {
            // Arrange
            $input = [10 => 'a', 20 => 'b', 30 => 'c'];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe([30 => 'c', 20 => 'b', 10 => 'a']);
        });

        test('reverses mixed type array', function (): void {
            // Arrange
            $input = [1, 'two', 3.0, false, null];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe([4 => null, 3 => false, 2 => 3.0, 1 => 'two', 0 => 1]);
        });

        test('works with large arrays efficiently', function (): void {
            // Arrange
            $input = range(1, 1_000);

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toHaveCount(1_000);
            expect($result[999])->toBe(1_000);
            expect($result[0])->toBe(1);
        });

        test('handles array with objects', function (): void {
            // Arrange
            $obj1 = (object) ['id' => 1];
            $obj2 = (object) ['id' => 2];
            $obj3 = (object) ['id' => 3];
            $input = [$obj1, $obj2, $obj3];

            // Act
            $result = reverse($input);

            // Assert
            expect($result[2])->toBe($obj3);
            expect($result[1])->toBe($obj2);
            expect($result[0])->toBe($obj1);
        });

        test('handles array with nested arrays', function (): void {
            // Arrange
            $input = [[1, 2], [3, 4], [5, 6]];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe([2 => [5, 6], 1 => [3, 4], 0 => [1, 2]]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: reverse() is type-safe at the PHP level and expects array
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns empty array for empty input', function (): void {
            // Arrange
            $input = [];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns same single element array', function (): void {
            // Arrange
            $input = [42];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe([0 => 42]);
        });

        test('reverses two element array', function (): void {
            // Arrange
            $input = ['first', 'second'];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe([1 => 'second', 0 => 'first']);
        });

        test('handles array with null values', function (): void {
            // Arrange
            $input = [1, null, 3, null, 5];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe([4 => 5, 3 => null, 2 => 3, 1 => null, 0 => 1]);
        });

        test('handles array with false values', function (): void {
            // Arrange
            $input = [true, false, true, false];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe([3 => false, 2 => true, 1 => false, 0 => true]);
        });

        test('handles array with zero values', function (): void {
            // Arrange
            $input = [0, 1, 0, 2, 0];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe([4 => 0, 3 => 2, 2 => 0, 1 => 1, 0 => 0]);
        });

        test('handles array with empty strings', function (): void {
            // Arrange
            $input = ['', 'a', '', 'b'];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe([3 => 'b', 2 => '', 1 => 'a', 0 => '']);
        });

        test('handles array with duplicate values', function (): void {
            // Arrange
            $input = [1, 2, 1, 2, 1];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe([4 => 1, 3 => 2, 2 => 1, 1 => 2, 0 => 1]);
        });

        test('does not mutate original array', function (): void {
            // Arrange
            $input = [1, 2, 3, 4];
            $original = $input;

            // Act
            $result = reverse($input);

            // Assert
            expect($input)->toBe($original);
            expect($result)->not->toBe($input);
        });

        test('reverses twice returns original', function (): void {
            // Arrange
            $input = [1, 2, 3, 4, 5];

            // Act
            $result = reverse(reverse($input));

            // Assert
            expect($result)->toBe($input);
        });

        test('handles array with special character keys', function (): void {
            // Arrange
            $input = ['key-1' => 'a', 'key.2' => 'b', 'key_3' => 'c'];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe(['key_3' => 'c', 'key.2' => 'b', 'key-1' => 'a']);
        });

        test('handles array with mixed string and numeric keys', function (): void {
            // Arrange
            $input = [0 => 'a', 'key' => 'b', 1 => 'c', 'other' => 'd'];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe(['other' => 'd', 1 => 'c', 'key' => 'b', 0 => 'a']);
        });

        test('handles array with negative numeric keys', function (): void {
            // Arrange
            $input = [-2 => 'a', -1 => 'b', 0 => 'c', 1 => 'd'];

            // Act
            $result = reverse($input);

            // Assert
            expect($result)->toBe([1 => 'd', 0 => 'c', -1 => 'b', -2 => 'a']);
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
