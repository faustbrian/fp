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

use function Cline\fp\map;
use function describe;
use function expect;
use function range;
use function test;

describe('map()', function (): void {
    describe('Happy Paths', function (): void {
        test('transforms array elements with custom callback', function (): void {
            // Arrange
            $double = map(fn (int $x): int => $x * 2);
            $input = [1, 2, 3];

            // Act
            $result = $double($input);

            // Assert
            expect($result)->toBe([2, 4, 6]);
        });

        test('transforms array with built-in PHP function', function (): void {
            // Arrange
            $uppercase = map(strtoupper(...));
            $input = ['foo', 'bar', 'baz'];

            // Act
            $result = $uppercase($input);

            // Assert
            expect($result)->toBe(['FOO', 'BAR', 'BAZ']);
        });

        test('preserves associative array keys', function (): void {
            // Arrange
            $addPrefix = map(fn (string $v): string => 'item_'.$v);
            $input = ['a' => '1', 'b' => '2', 'c' => '3'];

            // Act
            $result = $addPrefix($input);

            // Assert
            expect($result)->toBe(['a' => 'item_1', 'b' => 'item_2', 'c' => 'item_3']);
        });

        test('transforms iterator to array', function (): void {
            // Arrange
            $triple = map(fn (int $x): int => $x * 3);
            $gen = function (): Generator {
                yield 5;

                yield 10;

                yield 15;
            };

            // Act
            $result = $triple($gen());

            // Assert
            expect($result)->toBe([15, 30, 45]);
        });

        test('transforms ArrayIterator to array', function (): void {
            // Arrange
            $square = map(fn (int $x): int => $x ** 2);
            $iterator = new ArrayIterator([2, 3, 4]);

            // Act
            $result = $square($iterator);

            // Assert
            expect($result)->toBe([4, 9, 16]);
        });

        test('preserves numeric keys during transformation', function (): void {
            // Arrange
            $addTen = map(fn (int $x): int => $x + 10);
            $input = [10 => 5, 20 => 6, 30 => 7];

            // Act
            $result = $addTen($input);

            // Assert
            expect($result)->toBe([10 => 15, 20 => 16, 30 => 17]);
        });

        test('works with various data types', function (): void {
            // Arrange
            $stringify = map(fn (mixed $x): string => (string) $x);
            $input = [1, 2.5, true, null];

            // Act
            $result = $stringify($input);

            // Assert
            expect($result)->toBe(['1', '2.5', '1', '']);
        });

        test('chains multiple transformations', function (): void {
            // Arrange
            $double = map(fn (int $x): int => $x * 2);
            $addFive = map(fn (int $x): int => $x + 5);
            $input = [1, 2, 3];

            // Act
            $result = $addFive($double($input));

            // Assert
            expect($result)->toBe([7, 9, 11]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: map() is type-safe at the PHP level and expects callable + iterable
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('transforms empty array', function (): void {
            // Arrange
            $double = map(fn (int $x): int => $x * 2);
            $input = [];

            // Act
            $result = $double($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('transforms single element array', function (): void {
            // Arrange
            $negate = map(fn (int $x): int => -$x);
            $input = [42];

            // Act
            $result = $negate($input);

            // Assert
            expect($result)->toBe([-42]);
        });

        test('handles empty generator', function (): void {
            // Arrange
            $double = map(fn (int $x): int => $x * 2);
            $gen = function (): Generator {
                return;

                yield;
            };

            // Act
            $result = $double($gen());

            // Assert
            expect($result)->toBe([]);
        });

        test('handles complex object transformations', function (): void {
            // Arrange
            $extractName = map(fn (object $obj): string => $obj->name);
            $input = [
                (object) ['name' => 'Alice'],
                (object) ['name' => 'Bob'],
                (object) ['name' => 'Charlie'],
            ];

            // Act
            $result = $extractName($input);

            // Assert
            expect($result)->toBe(['Alice', 'Bob', 'Charlie']);
        });

        test('handles null values in array', function (): void {
            // Arrange
            $makeString = map(fn (?int $x): string => $x === null ? 'null' : (string) $x);
            $input = [1, null, 3, null, 5];

            // Act
            $result = $makeString($input);

            // Assert
            expect($result)->toBe(['1', 'null', '3', 'null', '5']);
        });

        test('preserves keys with string keys containing special characters', function (): void {
            // Arrange
            $double = map(fn (int $x): int => $x * 2);
            $input = ['key-1' => 10, 'key.2' => 20, 'key_3' => 30];

            // Act
            $result = $double($input);

            // Assert
            expect($result)->toBe(['key-1' => 20, 'key.2' => 40, 'key_3' => 60]);
        });

        test('handles large arrays efficiently', function (): void {
            // Arrange
            $increment = map(fn (int $x): int => $x + 1);
            $input = range(1, 1_000);

            // Act
            $result = $increment($input);

            // Assert
            expect($result)->toHaveCount(1_000);
            expect($result[0])->toBe(2);
            expect($result[999])->toBe(1_001);
        });

        test('handles mixed numeric and string keys', function (): void {
            // Arrange
            $double = map(fn (int $x): int => $x * 2);
            $input = [0 => 1, 'a' => 2, 1 => 3, 'b' => 4];

            // Act
            $result = $double($input);

            // Assert
            expect($result)->toBe([0 => 2, 'a' => 4, 1 => 6, 'b' => 8]);
        });

        test('works with generator yielding associative keys', function (): void {
            // Arrange
            $double = map(fn (int $x): int => $x * 2);
            $gen = function (): Generator {
                yield 'first' => 10;

                yield 'second' => 20;

                yield 'third' => 30;
            };

            // Act
            $result = $double($gen());

            // Assert
            expect($result)->toBe(['first' => 20, 'second' => 40, 'third' => 60]);
        });

        test('handles transformations returning same value', function (): void {
            // Arrange
            $identity = map(fn (mixed $x): mixed => $x);
            $input = ['a' => 1, 'b' => 2, 'c' => 3];

            // Act
            $result = $identity($input);

            // Assert
            expect($result)->toBe(['a' => 1, 'b' => 2, 'c' => 3]);
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
