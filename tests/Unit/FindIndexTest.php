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

use function Cline\fp\find;
use function Cline\fp\findIndex;
use function describe;
use function expect;
use function mb_strlen;
use function test;

describe('findIndex()', function (): void {
    describe('Happy Paths', function (): void {
        test('finds index of first even number', function (): void {
            // Arrange
            $findEven = findIndex(fn (int $x): bool => $x % 2 === 0);
            $input = [1, 3, 4, 6, 8];

            // Act
            $result = $findEven($input);

            // Assert
            expect($result)->toBe(2);
        });

        test('finds string key in associative array', function (): void {
            // Arrange
            $findPositive = findIndex(fn (int $x): bool => $x > 0);
            $input = ['a' => -1, 'b' => 5, 'c' => 10];

            // Act
            $result = $findPositive($input);

            // Assert
            expect($result)->toBe('b');
        });

        test('finds numeric key', function (): void {
            // Arrange
            $findLong = findIndex(fn (string $s): bool => mb_strlen($s) > 5);
            $input = ['hi', 'hello', 'wonderful', 'world'];

            // Act
            $result = $findLong($input);

            // Assert
            expect($result)->toBe(2);
        });

        test('works with generator', function (): void {
            // Arrange
            $findGreaterThanTen = findIndex(fn (int $x): bool => $x > 10);
            $gen = function (): Generator {
                yield 5;

                yield 8;

                yield 15;

                yield 20;
            };

            // Act
            $result = $findGreaterThanTen($gen());

            // Assert
            expect($result)->toBe(2);
        });

        test('works with ArrayIterator', function (): void {
            // Arrange
            $findNegative = findIndex(fn (int $x): bool => $x < 0);
            $iterator = new ArrayIterator([5, 10, -3, 7]);

            // Act
            $result = $findNegative($iterator);

            // Assert
            expect($result)->toBe(2);
        });

        test('finds object index matching condition', function (): void {
            // Arrange
            $findAdult = findIndex(fn (object $user): bool => $user->age >= 18);
            $input = [
                (object) ['name' => 'Alice', 'age' => 15],
                (object) ['name' => 'Bob', 'age' => 20],
                (object) ['name' => 'Charlie', 'age' => 25],
            ];

            // Act
            $result = $findAdult($input);

            // Assert
            expect($result)->toBe(1);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: findIndex() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns null when no match found', function (): void {
            // Arrange
            $findEven = findIndex(fn (int $x): bool => $x % 2 === 0);
            $input = [1, 3, 5, 7, 9];

            // Act
            $result = $findEven($input);

            // Assert
            expect($result)->toBeNull();
        });

        test('returns null for empty array', function (): void {
            // Arrange
            $findEven = findIndex(fn (int $x): bool => $x % 2 === 0);
            $input = [];

            // Act
            $result = $findEven($input);

            // Assert
            expect($result)->toBeNull();
        });

        test('returns null for empty generator', function (): void {
            // Arrange
            $findEven = findIndex(fn (int $x): bool => $x % 2 === 0);
            $gen = function (): Generator {
                return;

                yield;
            };

            // Act
            $result = $findEven($gen());

            // Assert
            expect($result)->toBeNull();
        });

        test('returns first index when multiple match', function (): void {
            // Arrange
            $findEven = findIndex(fn (int $x): bool => $x % 2 === 0);
            $input = [2, 4, 6, 8];

            // Act
            $result = $findEven($input);

            // Assert
            expect($result)->toBe(0);
        });

        test('finds index of single matching element', function (): void {
            // Arrange
            $findTen = findIndex(fn (int $x): bool => $x === 10);
            $input = [1, 2, 10, 3, 4];

            // Act
            $result = $findTen($input);

            // Assert
            expect($result)->toBe(2);
        });

        test('can find index 0', function (): void {
            // Arrange
            $findEven = findIndex(fn (int $x): bool => $x % 2 === 0);
            $input = [2, 1, 3, 5];

            // Act
            $result = $findEven($input);

            // Assert
            expect($result)->toBe(0);
        });

        test('distinguishes between null result and falsy values', function (): void {
            // Arrange
            $findZero = findIndex(fn (int $x): bool => $x === 0);
            $input = [0, 1, 2];

            // Act
            $result = $findZero($input);

            // Assert
            expect($result)->toBe(0);
            expect($result)->not->toBeNull();
        });

        test('handles string keys', function (): void {
            // Arrange
            $findValue = findIndex(fn (int $x): bool => $x > 5);
            $input = ['first' => 1, 'second' => 10, 'third' => 3];

            // Act
            $result = $findValue($input);

            // Assert
            expect($result)->toBe('second');
        });

        test('handles mixed key types', function (): void {
            // Arrange
            $findEven = findIndex(fn (int $x): bool => $x % 2 === 0);
            $input = [0 => 1, 'key' => 2, 1 => 3];

            // Act
            $result = $findEven($input);

            // Assert
            expect($result)->toBe('key');
        });

        test('preserves original key type', function (): void {
            // Arrange
            $findValue = findIndex(fn (int $x): bool => $x === 5);
            $input = [10 => 1, 20 => 5, 30 => 10];

            // Act
            $result = $findValue($input);

            // Assert
            expect($result)->toBe(20);
            expect($result)->toBeInt();
        });

        test('stops searching after first match', function (): void {
            // Arrange
            $callCount = 0;
            $findEven = findIndex(function (int $x) use (&$callCount): bool {
                ++$callCount;

                return $x % 2 === 0;
            });
            $input = [1, 3, 4, 6, 8];

            // Act
            $result = $findEven($input);

            // Assert
            expect($result)->toBe(2);
            expect($callCount)->toBe(3); // Only called for 1, 3, 4
        });

        test('can be curried and reused', function (): void {
            // Arrange
            $findNegative = findIndex(fn (int $x): bool => $x < 0);

            // Act
            $result1 = $findNegative([1, -2, 3]);
            $result2 = $findNegative([5, 6, -10]);

            // Assert
            expect($result1)->toBe(1);
            expect($result2)->toBe(2);
        });

        test('works with special character keys', function (): void {
            // Arrange
            $findValue = findIndex(fn (int $x): bool => $x > 10);
            $input = ['key-1' => 5, 'key.2' => 15, 'key_3' => 8];

            // Act
            $result = $findValue($input);

            // Assert
            expect($result)->toBe('key.2');
        });

        test('handles negative numeric keys', function (): void {
            // Arrange
            $findValue = findIndex(fn (int $x): bool => $x === 2);
            $input = [-2 => 1, -1 => 2, 0 => 3];

            // Act
            $result = $findValue($input);

            // Assert
            expect($result)->toBe(-1);
        });

        test('useful for finding position in list', function (): void {
            // Arrange
            $users = [
                ['id' => 1, 'name' => 'Alice'],
                ['id' => 2, 'name' => 'Bob'],
                ['id' => 3, 'name' => 'Charlie'],
            ];
            $findBobIndex = findIndex(fn (array $user): bool => $user['name'] === 'Bob');

            // Act
            $result = $findBobIndex($users);

            // Assert
            expect($result)->toBe(1);
        });

        test('complements find by returning index instead of value', function (): void {
            // Arrange
            $predicate = fn (int $x): bool => $x > 5;
            $input = [1, 3, 7, 9];
            $findResult = find($predicate);
            $findIndexResult = findIndex($predicate);

            // Act
            $value = $findResult($input);
            $index = $findIndexResult($input);

            // Assert
            expect($value)->toBe(7);
            expect($index)->toBe(2);
            expect($input[$index])->toBe($value);
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
