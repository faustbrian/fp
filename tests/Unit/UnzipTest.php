<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use InvalidArgumentException;
use stdClass;

use function Cline\fp\unzip;
use function Cline\fp\zip;
use function describe;
use function expect;
use function test;

describe('unzip', function (): void {
    describe('Happy Paths', function (): void {
        test('unzips array of pairs into two arrays', function (): void {
            // Arrange
            $tuples = [[1, 'a'], [2, 'b'], [3, 'c']];

            // Act
            $result = unzip($tuples);

            // Assert
            expect($result)->toBe([
                [1, 2, 3],
                ['a', 'b', 'c'],
            ]);
        });

        test('unzips array of triplets into three arrays', function (): void {
            // Arrange
            $tuples = [[1, 'a', true], [2, 'b', false], [3, 'c', true]];

            // Act
            $result = unzip($tuples);

            // Assert
            expect($result)->toBe([
                [1, 2, 3],
                ['a', 'b', 'c'],
                [true, false, true],
            ]);
        });

        test('round-trips with zip function', function (): void {
            // Arrange
            $array1 = [1, 2, 3];
            $array2 = ['a', 'b', 'c'];
            $array3 = [true, false, true];

            // Act
            $zipped = zip($array1, $array2, $array3);
            $result = unzip($zipped);

            // Assert
            expect($result)->toBe([
                [1, 2, 3],
                ['a', 'b', 'c'],
                [true, false, true],
            ]);
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception when element is not an array', function (): void {
            // Arrange
            $tuples = [[1, 'a'], 'not-an-array', [3, 'c']];

            // Act & Assert
            expect(fn (): array => unzip($tuples))
                ->toThrow(InvalidArgumentException::class, 'Each element must be an array');
        });

        test('throws exception when element is a scalar value', function (): void {
            // Arrange
            $tuples = [[1, 2], 123, [4, 5]];

            // Act & Assert
            expect(fn (): array => unzip($tuples))
                ->toThrow(InvalidArgumentException::class, 'Each element must be an array');
        });

        test('throws exception when element is null', function (): void {
            // Arrange
            $tuples = [[1, 2], null, [4, 5]];

            // Act & Assert
            expect(fn (): array => unzip($tuples))
                ->toThrow(InvalidArgumentException::class, 'Each element must be an array');
        });

        test('throws exception when element is an object', function (): void {
            // Arrange
            $tuples = [[1, 2], new stdClass(), [4, 5]];

            // Act & Assert
            expect(fn (): array => unzip($tuples))
                ->toThrow(InvalidArgumentException::class, 'Each element must be an array');
        });
    });

    describe('Edge Cases', function (): void {
        test('returns empty array when given empty array', function (): void {
            // Arrange
            $tuples = [];

            // Act
            $result = unzip($tuples);

            // Assert
            expect($result)->toBe([]);
        });

        test('unzips single tuple', function (): void {
            // Arrange
            $tuples = [[1, 'a', true]];

            // Act
            $result = unzip($tuples);

            // Assert
            expect($result)->toBe([
                [1],
                ['a'],
                [true],
            ]);
        });

        test('handles tuples of different lengths', function (): void {
            // Arrange
            $tuples = [[1, 'a'], [2, 'b', 'extra'], [3]];

            // Act
            $result = unzip($tuples);

            // Assert
            expect($result)->toBe([
                [1, 2, 3],
                ['a', 'b'],
                ['extra'],
            ]);
        });

        test('handles nested arrays as tuple elements', function (): void {
            // Arrange
            $tuples = [
                [[1, 2], ['a', 'b']],
                [[3, 4], ['c', 'd']],
                [[5, 6], ['e', 'f']],
            ];

            // Act
            $result = unzip($tuples);

            // Assert
            expect($result)->toBe([
                [[1, 2], [3, 4], [5, 6]],
                [['a', 'b'], ['c', 'd'], ['e', 'f']],
            ]);
        });

        test('handles mixed types in tuples', function (): void {
            // Arrange
            $tuples = [
                [1, 'string', true, null],
                [2.5, [], false, new stdClass()],
            ];

            // Act
            $result = unzip($tuples);

            // Assert
            expect($result[0])->toBe([1, 2.5]);
            expect($result[1])->toBe(['string', []]);
            expect($result[2])->toBe([true, false]);
            expect($result[3][0])->toBeNull();
            expect($result[3][1])->toBeInstanceOf(stdClass::class);
        });

        test('uses all values from associative arrays in tuples', function (): void {
            // Arrange
            $tuples = [
                ['x' => 1, 'y' => 2],
                ['x' => 3, 'y' => 4],
                ['x' => 5, 'y' => 6],
            ];

            // Act
            $result = unzip($tuples);

            // Assert
            expect($result)->toBe([
                [1, 3, 5],
                [2, 4, 6],
            ]);
        });

        test('handles single element tuples', function (): void {
            // Arrange
            $tuples = [[1], [2], [3]];

            // Act
            $result = unzip($tuples);

            // Assert
            expect($result)->toBe([
                [1, 2, 3],
            ]);
        });

        test('preserves order of elements within tuples', function (): void {
            // Arrange
            $tuples = [
                ['z', 'y', 'x'],
                ['c', 'b', 'a'],
            ];

            // Act
            $result = unzip($tuples);

            // Assert
            expect($result)->toBe([
                ['z', 'c'],
                ['y', 'b'],
                ['x', 'a'],
            ]);
        });

        test('handles tuples with numeric string keys', function (): void {
            // Arrange
            $tuples = [
                ['0' => 'first', '1' => 'second'],
                ['0' => 'third', '1' => 'fourth'],
            ];

            // Act
            $result = unzip($tuples);

            // Assert
            expect($result)->toBe([
                ['first', 'third'],
                ['second', 'fourth'],
            ]);
        });

        test('handles empty tuples within array', function (): void {
            // Arrange
            $tuples = [
                [1, 2],
                [],
                [3, 4],
            ];

            // Act
            $result = unzip($tuples);

            // Assert
            expect($result)->toBe([
                [1, 3],
                [2, 4],
            ]);
        });
    });
});
