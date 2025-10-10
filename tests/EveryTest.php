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

use function Cline\fp\all;
use function Cline\fp\every;
use function describe;
use function expect;
use function is_int;
use function test;

describe('every()', function (): void {
    describe('Happy Paths', function (): void {
        test('returns true when all elements match predicate', function (): void {
            // Arrange
            $allPositive = every(fn (int $n): bool => $n > 0);
            $input = [1, 2, 3, 4];

            // Act
            $result = $allPositive($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('returns false when not all elements match', function (): void {
            // Arrange
            $allPositive = every(fn (int $n): bool => $n > 0);
            $input = [1, -2, 3];

            // Act
            $result = $allPositive($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('works as alias for all', function (): void {
            // Arrange
            $everyEven = every(fn (int $n): bool => $n % 2 === 0);
            $allEven = all(fn (int $n): bool => $n % 2 === 0);
            $input = [2, 4, 6, 8];

            // Act
            $everyResult = $everyEven($input);
            $allResult = $allEven($input);

            // Assert
            expect($everyResult)->toBe($allResult);
            expect($everyResult)->toBeTrue();
        });

        test('validates all strings are non-empty', function (): void {
            // Arrange
            $allNonEmpty = every(fn (string $s): bool => $s !== '');
            $input = ['hello', 'world', 'foo'];

            // Act
            $result = $allNonEmpty($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('checks all numbers in range', function (): void {
            // Arrange
            $inRange = every(fn (int $n): bool => $n >= 1 && $n <= 10);
            $input = [1, 5, 8, 10];

            // Act
            $result = $inRange($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('works with generator', function (): void {
            // Arrange
            $allEven = every(fn (int $n): bool => $n % 2 === 0);
            $gen = function (): Generator {
                yield 2;

                yield 4;

                yield 6;
            };

            // Act
            $result = $allEven($gen());

            // Assert
            expect($result)->toBeTrue();
        });

        test('works with ArrayIterator', function (): void {
            // Arrange
            $allPositive = every(fn (int $n): bool => $n > 0);
            $iterator = new ArrayIterator([5, 10, 15]);

            // Act
            $result = $allPositive($iterator);

            // Assert
            expect($result)->toBeTrue();
        });
    });

    describe('Sad Paths', function (): void {
        // Note: every() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns true for empty array', function (): void {
            // Arrange
            $allEven = every(fn (int $n): bool => $n % 2 === 0);
            $input = [];

            // Act
            $result = $allEven($input);

            // Assert
            expect($result)->toBeTrue(); // Vacuous truth
        });

        test('returns true for empty generator', function (): void {
            // Arrange
            $allEven = every(fn (int $n): bool => $n % 2 === 0);
            $gen = function (): Generator {
                return;

                yield;
            };

            // Act
            $result = $allEven($gen());

            // Assert
            expect($result)->toBeTrue(); // Vacuous truth
        });

        test('returns true for single matching element', function (): void {
            // Arrange
            $isPositive = every(fn (int $n): bool => $n > 0);
            $input = [5];

            // Act
            $result = $isPositive($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('returns false for single non-matching element', function (): void {
            // Arrange
            $isPositive = every(fn (int $n): bool => $n > 0);
            $input = [-5];

            // Act
            $result = $isPositive($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('short-circuits on first non-match', function (): void {
            // Arrange
            $count = 0;
            $gen = function () use (&$count): Generator {
                yield ++$count; // 1

                yield ++$count; // 2

                yield ++$count; // 3

                yield ++$count; // 4
            };
            $allLessThanThree = every(fn (int $n): bool => $n < 3);

            // Act
            $result = $allLessThanThree($gen());

            // Assert
            expect($result)->toBeFalse();
            expect($count)->toBe(3); // Stopped at 3 which fails the test
        });

        test('handles null values', function (): void {
            // Arrange
            $allNull = every(fn (?int $n): bool => $n === null);
            $input = [null, null, null];

            // Act
            $result = $allNull($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('handles false values', function (): void {
            // Arrange
            $allFalse = every(fn (bool $b): bool => $b === false);
            $input = [false, false, false];

            // Act
            $result = $allFalse($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('distinguishes false from falsy values', function (): void {
            // Arrange
            $isFalse = every(fn (mixed $v): bool => $v === false);
            $input = [false, 0, false];

            // Act
            $result = $isFalse($input);

            // Assert
            expect($result)->toBeFalse(); // 0 is not false
        });

        test('handles zero values', function (): void {
            // Arrange
            $allZero = every(fn (int $n): bool => $n === 0);
            $input = [0, 0, 0];

            // Act
            $result = $allZero($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('handles empty strings', function (): void {
            // Arrange
            $allEmpty = every(fn (string $s): bool => $s === '');
            $input = ['', '', ''];

            // Act
            $result = $allEmpty($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('works with complex predicates', function (): void {
            // Arrange
            $complexCheck = every(fn (int $n): bool => $n > 0 && $n < 100 && $n % 2 === 0);
            $input = [2, 4, 6, 8, 10];

            // Act
            $result = $complexCheck($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('works with associative arrays', function (): void {
            // Arrange
            $allPositive = every(fn (int $n): bool => $n > 0);
            $input = ['a' => 10, 'b' => 20, 'c' => 30];

            // Act
            $result = $allPositive($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('can be reused with different inputs', function (): void {
            // Arrange
            $allEven = every(fn (int $n): bool => $n % 2 === 0);

            // Act
            $result1 = $allEven([2, 4, 6]);
            $result2 = $allEven([2, 3, 4]);

            // Assert
            expect($result1)->toBeTrue();
            expect($result2)->toBeFalse();
        });

        test('useful in JavaScript-style validation', function (): void {
            // Arrange
            $fields = ['name' => 'John', 'email' => 'john@example.com', 'age' => '25'];
            $allFilled = every(fn (string $v): bool => $v !== '');

            // Act
            $result = $allFilled($fields);

            // Assert
            expect($result)->toBeTrue();
        });

        test('validates array type uniformity', function (): void {
            // Arrange
            $allIntegers = every(fn (mixed $v): bool => is_int($v));
            $input = [1, 2, 3, 4];

            // Act
            $result = $allIntegers($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('detects type inconsistency', function (): void {
            // Arrange
            $allIntegers = every(fn (mixed $v): bool => is_int($v));
            $input = [1, 2, '3', 4];

            // Act
            $result = $allIntegers($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('works with object properties', function (): void {
            // Arrange
            $obj1 = (object) ['active' => true];
            $obj2 = (object) ['active' => true];
            $obj3 = (object) ['active' => true];
            $allActive = every(fn (object $obj): bool => $obj->active === true);
            $input = [$obj1, $obj2, $obj3];

            // Act
            $result = $allActive($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('validates all items pass multiple conditions', function (): void {
            // Arrange
            $validItems = every(fn (int $n): bool => $n > 0 && $n < 100);
            $input = [1, 50, 99];

            // Act
            $result = $validItems($input);

            // Assert
            expect($result)->toBeTrue();
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
