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

use function Cline\fp\any;
use function Cline\fp\some;
use function describe;
use function expect;
use function is_string;
use function mb_strlen;
use function test;

describe('some()', function (): void {
    describe('Happy Paths', function (): void {
        test('returns true when any element matches predicate', function (): void {
            // Arrange
            $hasEven = some(fn (int $n): bool => $n % 2 === 0);
            $input = [1, 3, 4, 5];

            // Act
            $result = $hasEven($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('returns false when no element matches', function (): void {
            // Arrange
            $hasEven = some(fn (int $n): bool => $n % 2 === 0);
            $input = [1, 3, 5];

            // Act
            $result = $hasEven($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('works as alias for any', function (): void {
            // Arrange
            $someEven = some(fn (int $n): bool => $n % 2 === 0);
            $anyEven = any(fn (int $n): bool => $n % 2 === 0);
            $input = [1, 2, 3, 5, 7, 9];

            // Act
            $someResult = $someEven($input);
            $anyResult = $anyEven($input);

            // Assert
            expect($someResult)->toBe($anyResult);
            expect($someResult)->toBeTrue();
        });

        test('finds positive number', function (): void {
            // Arrange
            $hasPositive = some(fn (int $n): bool => $n > 0);
            $input = [-5, -3, 0, 2];

            // Act
            $result = $hasPositive($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('finds string matching pattern', function (): void {
            // Arrange
            $hasLongWord = some(fn (string $s): bool => mb_strlen($s) > 5);
            $input = ['hi', 'hello', 'wonderful'];

            // Act
            $result = $hasLongWord($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('works with generator', function (): void {
            // Arrange
            $hasEven = some(fn (int $n): bool => $n % 2 === 0);
            $gen = function (): Generator {
                yield 1;

                yield 3;

                yield 4;

                yield 5;
            };

            // Act
            $result = $hasEven($gen());

            // Assert
            expect($result)->toBeTrue();
        });

        test('works with ArrayIterator', function (): void {
            // Arrange
            $hasNegative = some(fn (int $n): bool => $n < 0);
            $iterator = new ArrayIterator([5, 10, -1, 15]);

            // Act
            $result = $hasNegative($iterator);

            // Assert
            expect($result)->toBeTrue();
        });
    });

    describe('Sad Paths', function (): void {
        // Note: some() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns false for empty array', function (): void {
            // Arrange
            $hasEven = some(fn (int $n): bool => $n % 2 === 0);
            $input = [];

            // Act
            $result = $hasEven($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('returns false for empty generator', function (): void {
            // Arrange
            $hasEven = some(fn (int $n): bool => $n % 2 === 0);
            $gen = function (): Generator {
                return;

                yield;
            };

            // Act
            $result = $hasEven($gen());

            // Assert
            expect($result)->toBeFalse();
        });

        test('returns true for single matching element', function (): void {
            // Arrange
            $isPositive = some(fn (int $n): bool => $n > 0);
            $input = [5];

            // Act
            $result = $isPositive($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('returns false for single non-matching element', function (): void {
            // Arrange
            $isPositive = some(fn (int $n): bool => $n > 0);
            $input = [-5];

            // Act
            $result = $isPositive($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('short-circuits on first match', function (): void {
            // Arrange
            $count = 0;
            $gen = function () use (&$count): Generator {
                yield ++$count;

                yield ++$count;

                yield ++$count;

                yield ++$count;
            };
            $isTwo = some(fn (int $n): bool => $n === 2);

            // Act
            $result = $isTwo($gen());

            // Assert
            expect($result)->toBeTrue();
            expect($count)->toBe(2); // Stopped after finding match
        });

        test('handles null values', function (): void {
            // Arrange
            $hasNull = some(fn (?int $n): bool => $n === null);
            $input = [1, 2, null, 4];

            // Act
            $result = $hasNull($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('handles false values', function (): void {
            // Arrange
            $hasFalse = some(fn (bool $b): bool => $b === false);
            $input = [true, true, false, true];

            // Act
            $result = $hasFalse($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('distinguishes false from falsy values', function (): void {
            // Arrange
            $isFalse = some(fn (mixed $v): bool => $v === false);
            $input = [0, '', null];

            // Act
            $result = $isFalse($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('handles zero values', function (): void {
            // Arrange
            $isZero = some(fn (int $n): bool => $n === 0);
            $input = [1, 2, 0, 3];

            // Act
            $result = $isZero($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('handles empty strings', function (): void {
            // Arrange
            $isEmpty = some(fn (string $s): bool => $s === '');
            $input = ['a', '', 'b'];

            // Act
            $result = $isEmpty($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('works with complex predicates', function (): void {
            // Arrange
            $hasComplexMatch = some(fn (int $n): bool => $n > 5 && $n < 10 && $n % 2 === 0);
            $input = [1, 3, 8, 15];

            // Act
            $result = $hasComplexMatch($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('works with associative arrays', function (): void {
            // Arrange
            $hasHighValue = some(fn (int $n): bool => $n > 50);
            $input = ['a' => 10, 'b' => 20, 'c' => 60];

            // Act
            $result = $hasHighValue($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('can be reused with different inputs', function (): void {
            // Arrange
            $hasEven = some(fn (int $n): bool => $n % 2 === 0);

            // Act
            $result1 = $hasEven([1, 2, 3]);
            $result2 = $hasEven([1, 3, 5]);

            // Assert
            expect($result1)->toBeTrue();
            expect($result2)->toBeFalse();
        });

        test('useful in JavaScript-style validation', function (): void {
            // Arrange
            $errors = ['', '', 'Error: invalid input'];
            $hasErrors = some(fn (string $e): bool => $e !== '');

            // Act
            $result = $hasErrors($errors);

            // Assert
            expect($result)->toBeTrue();
        });

        test('checks array for specific type', function (): void {
            // Arrange
            $hasString = some(fn (mixed $v): bool => is_string($v));
            $input = [1, 2, 'hello', 4];

            // Act
            $result = $hasString($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('works with object properties', function (): void {
            // Arrange
            $obj1 = (object) ['active' => false];
            $obj2 = (object) ['active' => true];
            $obj3 = (object) ['active' => false];
            $hasActive = some(fn (object $obj): bool => $obj->active === true);
            $input = [$obj1, $obj2, $obj3];

            // Act
            $result = $hasActive($input);

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
