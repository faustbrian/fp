<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use stdClass;

use function Cline\fp\unique;
use function describe;
use function expect;
use function test;

describe('unique', function (): void {
    describe('Happy Paths', function (): void {
        test('removes duplicate integers', function (): void {
            // Arrange
            $input = [1, 2, 2, 3, 3, 3, 4];

            // Act
            $result = unique()($input);

            // Assert
            expect($result)->toBe([0 => 1, 1 => 2, 3 => 3, 6 => 4]);
        });

        test('removes duplicate strings', function (): void {
            // Arrange
            $input = ['apple', 'banana', 'apple', 'cherry', 'banana'];

            // Act
            $result = unique()($input);

            // Assert
            expect($result)->toBe([0 => 'apple', 1 => 'banana', 3 => 'cherry']);
        });

        test('preserves keys of first occurrence', function (): void {
            // Arrange
            $input = ['a' => 1, 'b' => 2, 'c' => 1, 'd' => 3];

            // Act
            $result = unique()($input);

            // Assert
            expect($result)->toBe(['a' => 1, 'b' => 2, 'd' => 3]);
        });

        test('returns empty array for empty input', function (): void {
            // Arrange
            $input = [];

            // Act
            $result = unique()($input);

            // Assert
            expect($result)->toBe([]);
        });
    });

    describe('Edge Cases', function (): void {
        test('returns same array when all values are unique', function (): void {
            // Arrange
            $input = [1, 2, 3, 4, 5];

            // Act
            $result = unique()($input);

            // Assert
            expect($result)->toBe([0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5]);
        });

        test('returns single element when all values are duplicates', function (): void {
            // Arrange
            $input = [5, 5, 5, 5, 5];

            // Act
            $result = unique()($input);

            // Assert
            expect($result)->toBe([0 => 5]);
        });

        test('handles mixed types correctly', function (): void {
            // Arrange
            $input = [1, '1', 2, '2', 1, 2];

            // Act
            $result = unique()($input);

            // Assert
            expect($result)->toBe([0 => 1, 1 => '1', 2 => 2, 3 => '2']);
        });

        test('distinguishes different object instances', function (): void {
            // Arrange
            $obj1 = new stdClass();
            $obj1->id = 1;

            $obj2 = new stdClass();
            $obj2->id = 1;

            $obj3 = $obj1;

            $input = [$obj1, $obj2, $obj3];

            // Act
            $result = unique()($input);

            // Assert - obj1 and obj2 are different objects, obj3 is same as obj1
            expect($result)->toHaveCount(2);
            expect($result[0])->toBe($obj1);
            expect($result[1])->toBe($obj2);
        });

        test('handles nested arrays correctly', function (): void {
            // Arrange
            $input = [
                [1, 2],
                [3, 4],
                [1, 2],
                [5, 6],
                [3, 4],
            ];

            // Act
            $result = unique()($input);

            // Assert
            expect($result)->toBe([
                0 => [1, 2],
                1 => [3, 4],
                3 => [5, 6],
            ]);
        });

        test('handles null and boolean values', function (): void {
            // Arrange
            $input = [null, true, false, null, true, false, 0, ''];

            // Act
            $result = unique()($input);

            // Assert
            expect($result)->toBe([
                0 => null,
                1 => true,
                2 => false,
                6 => 0,
                7 => '',
            ]);
        });

        test('handles float values with precision', function (): void {
            // Arrange
            $input = [1.0, 1.00, 2.5, 2.50, 3.0];

            // Act
            $result = unique()($input);

            // Assert
            expect($result)->toBe([0 => 1.0, 2 => 2.5, 4 => 3.0]);
        });

        test('preserves string keys with numeric values', function (): void {
            // Arrange
            $input = ['first' => 10, 'second' => 20, 'third' => 10, 'fourth' => 30];

            // Act
            $result = unique()($input);

            // Assert
            expect($result)->toBe(['first' => 10, 'second' => 20, 'fourth' => 30]);
        });
    });
});
