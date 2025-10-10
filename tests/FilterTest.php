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

use function array_values;
use function Cline\fp\filter;
use function describe;
use function expect;
use function max;
use function min;
use function range;
use function test;

describe('filter()', function (): void {
    describe('Happy Paths', function (): void {
        test('filters array elements with custom predicate', function (): void {
            // Arrange
            $isEven = filter(fn (int $x): bool => $x % 2 === 0);
            $input = [1, 2, 3, 4, 5, 6];

            // Act
            $result = $isEven($input);

            // Assert
            expect($result)->toBe([1 => 2, 3 => 4, 5 => 6]);
        });

        test('filters array with built-in PHP function', function (): void {
            // Arrange
            $isNumeric = filter('is_numeric');
            $input = ['a', '10', 'b', '20', 'c'];

            // Act
            $result = $isNumeric($input);

            // Assert
            expect($result)->toBe([1 => '10', 3 => '20']);
        });

        test('filters with default predicate removing falsy values', function (): void {
            // Arrange
            $filterTruthy = filter();
            $input = [0, 1, false, 'text', null, '', 42];

            // Act
            $result = $filterTruthy($input);

            // Assert
            expect($result)->toBe([1 => 1, 3 => 'text', 6 => 42]);
        });

        test('preserves associative array keys after filtering', function (): void {
            // Arrange
            $notNull = filter(fn (?int $x): bool => $x !== null);
            $input = ['a' => 1, 'b' => null, 'c' => 3];

            // Act
            $result = $notNull($input);

            // Assert
            expect($result)->toBe(['a' => 1, 'c' => 3]);
        });

        test('filters iterator to array', function (): void {
            // Arrange
            $greaterThanTen = filter(fn (int $x): bool => $x > 10);
            $gen = function (): Generator {
                yield 5;

                yield 15;

                yield 8;

                yield 20;
            };

            // Act
            $result = $greaterThanTen($gen());

            // Assert
            expect($result)->toBe([1 => 15, 3 => 20]);
        });

        test('filters ArrayIterator to array', function (): void {
            // Arrange
            $isPositive = filter(fn (int $x): bool => $x > 0);
            $iterator = new ArrayIterator([-5, 10, -3, 7]);

            // Act
            $result = $isPositive($iterator);

            // Assert
            expect($result)->toBe([1 => 10, 3 => 7]);
        });

        test('preserves numeric keys during filtering', function (): void {
            // Arrange
            $isEven = filter(fn (int $x): bool => $x % 2 === 0);
            $input = [10 => 2, 20 => 3, 30 => 4, 40 => 5];

            // Act
            $result = $isEven($input);

            // Assert
            expect($result)->toBe([10 => 2, 30 => 4]);
        });

        test('works with string predicates', function (): void {
            // Arrange
            $isString = filter('is_string');
            $input = [1, 'hello', 2.5, 'world', true];

            // Act
            $result = $isString($input);

            // Assert
            expect($result)->toBe([1 => 'hello', 3 => 'world']);
        });

        test('chains multiple filters', function (): void {
            // Arrange
            $isEven = filter(fn (int $x): bool => $x % 2 === 0);
            $greaterThanFive = filter(fn (int $x): bool => $x > 5);
            $input = [1, 2, 4, 6, 8, 10];

            // Act
            $result = $greaterThanFive($isEven($input));

            // Assert
            expect($result)->toBe([3 => 6, 4 => 8, 5 => 10]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: filter() is type-safe at the PHP level and expects callable|null + iterable
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('filters empty array', function (): void {
            // Arrange
            $isEven = filter(fn (int $x): bool => $x % 2 === 0);
            $input = [];

            // Act
            $result = $isEven($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns empty array when no elements match predicate', function (): void {
            // Arrange
            $greaterThanHundred = filter(fn (int $x): bool => $x > 100);
            $input = [1, 2, 3, 4, 5];

            // Act
            $result = $greaterThanHundred($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns all elements when all match predicate', function (): void {
            // Arrange
            $isPositive = filter(fn (int $x): bool => $x > 0);
            $input = [1, 2, 3, 4, 5];

            // Act
            $result = $isPositive($input);

            // Assert
            expect($result)->toBe([0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5]);
        });

        test('filters single element array that matches', function (): void {
            // Arrange
            $isEven = filter(fn (int $x): bool => $x % 2 === 0);
            $input = [42];

            // Act
            $result = $isEven($input);

            // Assert
            expect($result)->toBe([0 => 42]);
        });

        test('filters single element array that does not match', function (): void {
            // Arrange
            $isEven = filter(fn (int $x): bool => $x % 2 === 0);
            $input = [41];

            // Act
            $result = $isEven($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles empty generator', function (): void {
            // Arrange
            $isEven = filter(fn (int $x): bool => $x % 2 === 0);
            $gen = function (): Generator {
                return;

                yield;
            };

            // Act
            $result = $isEven($gen());

            // Assert
            expect($result)->toBe([]);
        });

        test('filters with default predicate on all falsy values', function (): void {
            // Arrange
            $filterTruthy = filter();
            $input = [0, false, null, '', '0', []];

            // Act
            $result = $filterTruthy($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('filters with default predicate on all truthy values', function (): void {
            // Arrange
            $filterTruthy = filter();
            $input = [1, 'hello', true, [1], 42];

            // Act
            $result = $filterTruthy($input);

            // Assert
            expect($result)->toBe([0 => 1, 1 => 'hello', 2 => true, 3 => [1], 4 => 42]);
        });

        test('handles complex object filtering', function (): void {
            // Arrange
            $isActive = filter(fn (object $obj): bool => $obj->active === true);
            $input = [
                (object) ['name' => 'Alice', 'active' => true],
                (object) ['name' => 'Bob', 'active' => false],
                (object) ['name' => 'Charlie', 'active' => true],
            ];

            // Act
            $result = $isActive($input);

            // Assert
            expect(array_values($result))->toHaveCount(2);
            expect($result[0]->name)->toBe('Alice');
            expect($result[2]->name)->toBe('Charlie');
        });

        test('preserves keys with string keys containing special characters', function (): void {
            // Arrange
            $isEven = filter(fn (int $x): bool => $x % 2 === 0);
            $input = ['key-1' => 10, 'key.2' => 21, 'key_3' => 30];

            // Act
            $result = $isEven($input);

            // Assert
            expect($result)->toBe(['key-1' => 10, 'key_3' => 30]);
        });

        test('handles large arrays efficiently', function (): void {
            // Arrange
            $isEven = filter(fn (int $x): bool => $x % 2 === 0);
            $input = range(1, 1_000);

            // Act
            $result = $isEven($input);

            // Assert
            expect($result)->toHaveCount(500);
            expect(min($result))->toBe(2);
            expect(max($result))->toBe(1_000);
        });

        test('handles mixed numeric and string keys', function (): void {
            // Arrange
            $isEven = filter(fn (int $x): bool => $x % 2 === 0);
            $input = [0 => 1, 'a' => 2, 1 => 3, 'b' => 4];

            // Act
            $result = $isEven($input);

            // Assert
            expect($result)->toBe(['a' => 2, 'b' => 4]);
        });

        test('works with generator yielding associative keys', function (): void {
            // Arrange
            $greaterThanTen = filter(fn (int $x): bool => $x > 10);
            $gen = function (): Generator {
                yield 'first' => 5;

                yield 'second' => 15;

                yield 'third' => 8;

                yield 'fourth' => 20;
            };

            // Act
            $result = $greaterThanTen($gen());

            // Assert
            expect($result)->toBe(['second' => 15, 'fourth' => 20]);
        });

        test('handles null values with null-aware predicate', function (): void {
            // Arrange
            $notNull = filter(fn (?int $x): bool => $x !== null);
            $input = [1, null, 3, null, 5];

            // Act
            $result = $notNull($input);

            // Assert
            expect($result)->toBe([0 => 1, 2 => 3, 4 => 5]);
        });

        test('filters array with only null values using default predicate', function (): void {
            // Arrange
            $filterTruthy = filter();
            $input = [null, null, null];

            // Act
            $result = $filterTruthy($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles boolean values correctly', function (): void {
            // Arrange
            $isTrue = filter(fn (bool $x): bool => $x);
            $input = [true, false, true, false, true];

            // Act
            $result = $isTrue($input);

            // Assert
            expect($result)->toBe([0 => true, 2 => true, 4 => true]);
        });

        test('filters with predicate always returning true', function (): void {
            // Arrange
            $alwaysTrue = filter(fn (mixed $x): bool => true);
            $input = ['a' => 1, 'b' => 2, 'c' => 3];

            // Act
            $result = $alwaysTrue($input);

            // Assert
            expect($result)->toBe(['a' => 1, 'b' => 2, 'c' => 3]);
        });

        test('filters with predicate always returning false', function (): void {
            // Arrange
            $alwaysFalse = filter(fn (mixed $x): bool => false);
            $input = ['a' => 1, 'b' => 2, 'c' => 3];

            // Act
            $result = $alwaysFalse($input);

            // Assert
            expect($result)->toBe([]);
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
