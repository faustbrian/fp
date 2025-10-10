<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\zip;
use function describe;
use function expect;
use function test;

describe('zip', function (): void {
    describe('Happy Paths', function (): void {
        test('combines two arrays of equal length element-wise', function (): void {
            // Arrange
            $array1 = [1, 2, 3];
            $array2 = ['a', 'b', 'c'];

            // Act
            $result = zip($array1, $array2);

            // Assert
            expect($result)->toBe([
                [1, 'a'],
                [2, 'b'],
                [3, 'c'],
            ]);
        });

        test('combines three arrays element-wise', function (): void {
            // Arrange
            $array1 = [1, 2, 3];
            $array2 = ['a', 'b', 'c'];
            $array3 = [true, false, true];

            // Act
            $result = zip($array1, $array2, $array3);

            // Assert
            expect($result)->toBe([
                [1, 'a', true],
                [2, 'b', false],
                [3, 'c', true],
            ]);
        });

        test('returns empty array when given empty array argument', function (): void {
            // Arrange
            $array1 = [1, 2, 3];
            $array2 = [];

            // Act
            $result = zip($array1, $array2);

            // Assert
            expect($result)->toBe([]);
        });
    });

    describe('Sad Paths', function (): void {
        test('stops at length of shortest array when arrays differ in length', function (): void {
            // Arrange
            $array1 = [1, 2, 3, 4, 5];
            $array2 = ['a', 'b', 'c'];
            $array3 = [true, false];

            // Act
            $result = zip($array1, $array2, $array3);

            // Assert
            expect($result)->toBe([
                [1, 'a', true],
                [2, 'b', false],
            ]);
        });

        test('returns empty array when called with no arguments', function (): void {
            // Arrange - no arguments

            // Act
            $result = zip();

            // Assert
            expect($result)->toBe([]);
        });
    });

    describe('Edge Cases', function (): void {
        test('wraps single array elements into single-element arrays', function (): void {
            // Arrange
            $array = [1, 2, 3];

            // Act
            $result = zip($array);

            // Assert
            expect($result)->toBe([
                [1],
                [2],
                [3],
            ]);
        });

        test('preserves values and ignores keys from input arrays', function (): void {
            // Arrange
            $array1 = [10 => 'first', 20 => 'second', 30 => 'third'];
            $array2 = [100 => 'A', 200 => 'B', 300 => 'C'];

            // Act
            $result = zip($array1, $array2);

            // Assert
            expect($result)->toBe([
                ['first', 'A'],
                ['second', 'B'],
                ['third', 'C'],
            ]);
        });

        test('uses values only from associative arrays with string keys', function (): void {
            // Arrange
            $array1 = ['one' => 1, 'two' => 2, 'three' => 3];
            $array2 = ['a' => 'A', 'b' => 'B', 'c' => 'C'];

            // Act
            $result = zip($array1, $array2);

            // Assert
            expect($result)->toBe([
                [1, 'A'],
                [2, 'B'],
                [3, 'C'],
            ]);
        });

        test('handles mixed types within arrays', function (): void {
            // Arrange
            $array1 = [1, 'two', 3.0];
            $array2 = [null, true, []];

            // Act
            $result = zip($array1, $array2);

            // Assert
            expect($result)->toBe([
                [1, null],
                ['two', true],
                [3.0, []],
            ]);
        });

        test('handles single element arrays', function (): void {
            // Arrange
            $array1 = [1];
            $array2 = ['a'];
            $array3 = [true];

            // Act
            $result = zip($array1, $array2, $array3);

            // Assert
            expect($result)->toBe([
                [1, 'a', true],
            ]);
        });

        test('returns empty when all arrays are empty', function (): void {
            // Arrange
            $array1 = [];
            $array2 = [];
            $array3 = [];

            // Act
            $result = zip($array1, $array2, $array3);

            // Assert
            expect($result)->toBe([]);
        });
    });
});
