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
use function Cline\fp\first;
use function describe;
use function expect;
use function mb_strlen;
use function test;

describe('find()', function (): void {
    describe('Happy Paths', function (): void {
        test('finds first even number', function (): void {
            // Arrange
            $findEven = find(fn (int $x): bool => $x % 2 === 0);
            $input = [1, 3, 4, 6, 8];

            // Act
            $result = $findEven($input);

            // Assert
            expect($result)->toBe(4);
        });

        test('finds first string matching condition', function (): void {
            // Arrange
            $findLong = find(fn (string $s): bool => mb_strlen($s) > 5);
            $input = ['hi', 'hello', 'wonderful', 'world'];

            // Act
            $result = $findLong($input);

            // Assert
            expect($result)->toBe('wonderful');
        });

        test('finds first element in associative array', function (): void {
            // Arrange
            $findPositive = find(fn (int $x): bool => $x > 0);
            $input = ['a' => -1, 'b' => 5, 'c' => 10];

            // Act
            $result = $findPositive($input);

            // Assert
            expect($result)->toBe(5);
        });

        test('works with generator', function (): void {
            // Arrange
            $findGreaterThanTen = find(fn (int $x): bool => $x > 10);
            $gen = function (): Generator {
                yield 5;

                yield 8;

                yield 15;

                yield 20;
            };

            // Act
            $result = $findGreaterThanTen($gen());

            // Assert
            expect($result)->toBe(15);
        });

        test('works with ArrayIterator', function (): void {
            // Arrange
            $findNegative = find(fn (int $x): bool => $x < 0);
            $iterator = new ArrayIterator([5, 10, -3, 7]);

            // Act
            $result = $findNegative($iterator);

            // Assert
            expect($result)->toBe(-3);
        });

        test('finds object matching condition', function (): void {
            // Arrange
            $findAdult = find(fn (object $user): bool => $user->age >= 18);
            $input = [
                (object) ['name' => 'Alice', 'age' => 15],
                (object) ['name' => 'Bob', 'age' => 20],
                (object) ['name' => 'Charlie', 'age' => 25],
            ];

            // Act
            $result = $findAdult($input);

            // Assert
            expect($result->name)->toBe('Bob');
            expect($result->age)->toBe(20);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: find() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns null when no match found', function (): void {
            // Arrange
            $findEven = find(fn (int $x): bool => $x % 2 === 0);
            $input = [1, 3, 5, 7, 9];

            // Act
            $result = $findEven($input);

            // Assert
            expect($result)->toBeNull();
        });

        test('returns null for empty array', function (): void {
            // Arrange
            $findEven = find(fn (int $x): bool => $x % 2 === 0);
            $input = [];

            // Act
            $result = $findEven($input);

            // Assert
            expect($result)->toBeNull();
        });

        test('returns null for empty generator', function (): void {
            // Arrange
            $findEven = find(fn (int $x): bool => $x % 2 === 0);
            $gen = function (): Generator {
                return;

                yield;
            };

            // Act
            $result = $findEven($gen());

            // Assert
            expect($result)->toBeNull();
        });

        test('finds first element when multiple match', function (): void {
            // Arrange
            $findEven = find(fn (int $x): bool => $x % 2 === 0);
            $input = [2, 4, 6, 8];

            // Act
            $result = $findEven($input);

            // Assert
            expect($result)->toBe(2);
        });

        test('finds single matching element', function (): void {
            // Arrange
            $findTen = find(fn (int $x): bool => $x === 10);
            $input = [1, 2, 10, 3, 4];

            // Act
            $result = $findTen($input);

            // Assert
            expect($result)->toBe(10);
        });

        test('can find null value', function (): void {
            // Arrange
            $findNull = find(fn (?int $x): bool => $x === null);
            $input = [1, 2, null, 3];

            // Act
            $result = $findNull($input);

            // Assert
            expect($result)->toBeNull();
        });

        test('can find false value', function (): void {
            // Arrange
            $findFalse = find(fn (bool $x): bool => $x === false);
            $input = [true, true, false, true];

            // Act
            $result = $findFalse($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('can find zero value', function (): void {
            // Arrange
            $findZero = find(fn (int $x): bool => $x === 0);
            $input = [1, 2, 0, 3];

            // Act
            $result = $findZero($input);

            // Assert
            expect($result)->toBe(0);
        });

        test('can find empty string', function (): void {
            // Arrange
            $findEmpty = find(fn (string $x): bool => $x === '');
            $input = ['a', 'b', '', 'c'];

            // Act
            $result = $findEmpty($input);

            // Assert
            expect($result)->toBe('');
        });

        test('stops searching after first match', function (): void {
            // Arrange
            $callCount = 0;
            $findEven = find(function (int $x) use (&$callCount): bool {
                ++$callCount;

                return $x % 2 === 0;
            });
            $input = [1, 3, 4, 6, 8];

            // Act
            $result = $findEven($input);

            // Assert
            expect($result)->toBe(4);
            expect($callCount)->toBe(3); // Only called for 1, 3, 4
        });

        test('can be curried and reused', function (): void {
            // Arrange
            $findNegative = find(fn (int $x): bool => $x < 0);

            // Act
            $result1 = $findNegative([1, -2, 3]);
            $result2 = $findNegative([5, 6, -10]);

            // Assert
            expect($result1)->toBe(-2);
            expect($result2)->toBe(-10);
        });

        test('works as alias for first', function (): void {
            // Arrange
            $predicate = fn (int $x): bool => $x > 5;
            $findResult = find($predicate);
            $firstResult = first($predicate);
            $input = [1, 3, 7, 9];

            // Act
            $found = $findResult($input);
            $first = $firstResult($input);

            // Assert
            expect($found)->toBe($first);
            expect($found)->toBe(7);
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
