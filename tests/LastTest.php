<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use ArrayIterator;
use Generator;

use function Cline\fp\last;
use function describe;
use function expect;
use function range;
use function test;

describe('last()', function (): void {
    describe('Happy Paths', function (): void {
        test('returns last element from numeric array', function (): void {
            // Arrange
            $input = [1, 2, 3, 4, 5];

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBe(5);
        });

        test('returns last element from associative array', function (): void {
            // Arrange
            $input = ['a' => 1, 'b' => 2, 'c' => 3];

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBe(3);
        });

        test('returns last element from string array', function (): void {
            // Arrange
            $input = ['hello', 'world', 'foo', 'bar'];

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBe('bar');
        });

        test('returns last element from generator', function (): void {
            // Arrange
            $gen = function (): Generator {
                yield 10;

                yield 20;

                yield 30;

                yield 40;
            };

            // Act
            $result = last($gen());

            // Assert
            expect($result)->toBe(40);
        });

        test('returns last element from ArrayIterator', function (): void {
            // Arrange
            $iterator = new ArrayIterator([5, 10, 15, 20]);

            // Act
            $result = last($iterator);

            // Assert
            expect($result)->toBe(20);
        });

        test('works with large arrays efficiently', function (): void {
            // Arrange
            $input = range(1, 1_000);

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBe(1_000);
        });

        test('preserves value type for mixed array', function (): void {
            // Arrange
            $input = [1, 'two', 3.0, false, null];

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBeNull();
        });

        test('returns last element with preserved keys', function (): void {
            // Arrange
            $input = [10 => 'a', 20 => 'b', 30 => 'c'];

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBe('c');
        });
    });

    describe('Sad Paths', function (): void {
        // Note: last() is type-safe at the PHP level and expects iterable
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns null for empty array', function (): void {
            // Arrange
            $input = [];

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBeNull();
        });

        test('returns single element from single element array', function (): void {
            // Arrange
            $input = [42];

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBe(42);
        });

        test('returns null from empty generator', function (): void {
            // Arrange
            $gen = function (): Generator {
                return;

                yield;
            };

            // Act
            $result = last($gen());

            // Assert
            expect($result)->toBeNull();
        });

        test('handles array with null value as last element', function (): void {
            // Arrange
            $input = [1, 2, 3, null];

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBeNull();
        });

        test('handles array with false as last element', function (): void {
            // Arrange
            $input = [true, true, false];

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('handles array with zero as last element', function (): void {
            // Arrange
            $input = [1, 2, 3, 0];

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBe(0);
        });

        test('handles array with empty string as last element', function (): void {
            // Arrange
            $input = ['a', 'b', ''];

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBe('');
        });

        test('returns last element from generator with single yield', function (): void {
            // Arrange
            $gen = function (): Generator {
                yield 'only-one';
            };

            // Act
            $result = last($gen());

            // Assert
            expect($result)->toBe('only-one');
        });

        test('handles generator with associative keys', function (): void {
            // Arrange
            $gen = function (): Generator {
                yield 'first' => 1;

                yield 'second' => 2;

                yield 'third' => 3;
            };

            // Act
            $result = last($gen());

            // Assert
            expect($result)->toBe(3);
        });

        test('handles array with objects', function (): void {
            // Arrange
            $obj1 = (object) ['id' => 1];
            $obj2 = (object) ['id' => 2];
            $obj3 = (object) ['id' => 3];
            $input = [$obj1, $obj2, $obj3];

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBe($obj3);
            expect($result->id)->toBe(3);
        });

        test('handles array with nested arrays', function (): void {
            // Arrange
            $input = [[1, 2], [3, 4], [5, 6]];

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBe([5, 6]);
        });

        test('handles array with special characters in string keys', function (): void {
            // Arrange
            $input = ['key-1' => 'a', 'key.2' => 'b', 'key_3' => 'c'];

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBe('c');
        });

        test('returns last element from array with duplicate values', function (): void {
            // Arrange
            $input = [1, 2, 1, 2, 1];

            // Act
            $result = last($input);

            // Assert
            expect($result)->toBe(1);
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
