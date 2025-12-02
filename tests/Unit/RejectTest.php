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
use function Cline\fp\reject;
use function describe;
use function expect;
use function test;

describe('reject()', function (): void {
    describe('Happy Paths', function (): void {
        test('rejects even numbers from array', function (): void {
            // Arrange
            $rejectEven = reject(fn (int $x): bool => $x % 2 === 0);
            $input = [1, 2, 3, 4, 5, 6];

            // Act
            $result = $rejectEven($input);

            // Assert
            expect($result)->toBe([0 => 1, 2 => 3, 4 => 5]);
        });

        test('rejects null values from array', function (): void {
            // Arrange
            $rejectNull = reject(fn (?int $x): bool => $x === null);
            $input = [1, null, 3, null, 5];

            // Act
            $result = $rejectNull($input);

            // Assert
            expect($result)->toBe([0 => 1, 2 => 3, 4 => 5]);
        });

        test('rejects strings matching condition', function (): void {
            // Arrange
            $rejectEmpty = reject(fn (string $s): bool => $s === '');
            $input = ['hello', '', 'world', ''];

            // Act
            $result = $rejectEmpty($input);

            // Assert
            expect($result)->toBe([0 => 'hello', 2 => 'world']);
        });

        test('preserves associative keys', function (): void {
            // Arrange
            $rejectNegative = reject(fn (int $x): bool => $x < 0);
            $input = ['a' => 1, 'b' => -2, 'c' => 3];

            // Act
            $result = $rejectNegative($input);

            // Assert
            expect($result)->toBe(['a' => 1, 'c' => 3]);
        });

        test('works with generator', function (): void {
            // Arrange
            $rejectEven = reject(fn (int $x): bool => $x % 2 === 0);
            $gen = function (): Generator {
                yield 1;

                yield 2;

                yield 3;

                yield 4;
            };

            // Act
            $result = $rejectEven($gen());

            // Assert
            expect($result)->toBe([0 => 1, 2 => 3]);
        });

        test('works with ArrayIterator', function (): void {
            // Arrange
            $rejectOdd = reject(fn (int $x): bool => $x % 2 !== 0);
            $iterator = new ArrayIterator([1, 2, 3, 4]);

            // Act
            $result = $rejectOdd($iterator);

            // Assert
            expect($result)->toBe([1 => 2, 3 => 4]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: reject() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns empty array when all elements rejected', function (): void {
            // Arrange
            $rejectAll = reject(fn (mixed $x): bool => true);
            $input = [1, 2, 3];

            // Act
            $result = $rejectAll($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns all elements when nothing rejected', function (): void {
            // Arrange
            $rejectNone = reject(fn (mixed $x): bool => false);
            $input = [1, 2, 3];

            // Act
            $result = $rejectNone($input);

            // Assert
            expect($result)->toBe([0 => 1, 1 => 2, 2 => 3]);
        });

        test('handles empty array', function (): void {
            // Arrange
            $rejectEven = reject(fn (int $x): bool => $x % 2 === 0);
            $input = [];

            // Act
            $result = $rejectEven($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles single element that matches predicate', function (): void {
            // Arrange
            $rejectEven = reject(fn (int $x): bool => $x % 2 === 0);
            $input = [2];

            // Act
            $result = $rejectEven($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles single element that does not match predicate', function (): void {
            // Arrange
            $rejectEven = reject(fn (int $x): bool => $x % 2 === 0);
            $input = [1];

            // Act
            $result = $rejectEven($input);

            // Assert
            expect($result)->toBe([0 => 1]);
        });

        test('handles empty generator', function (): void {
            // Arrange
            $rejectEven = reject(fn (int $x): bool => $x % 2 === 0);
            $gen = function (): Generator {
                return;

                yield;
            };

            // Act
            $result = $rejectEven($gen());

            // Assert
            expect($result)->toBe([]);
        });

        test('rejects false values correctly', function (): void {
            // Arrange
            $rejectFalse = reject(fn (bool $x): bool => $x === false);
            $input = [true, false, true, false];

            // Act
            $result = $rejectFalse($input);

            // Assert
            expect($result)->toBe([0 => true, 2 => true]);
        });

        test('rejects zero values correctly', function (): void {
            // Arrange
            $rejectZero = reject(fn (int $x): bool => $x === 0);
            $input = [0, 1, 0, 2, 0];

            // Act
            $result = $rejectZero($input);

            // Assert
            expect($result)->toBe([1 => 1, 3 => 2]);
        });

        test('handles objects with predicate', function (): void {
            // Arrange
            $rejectInactive = reject(fn (object $obj): bool => !$obj->active);
            $input = [
                (object) ['active' => true],
                (object) ['active' => false],
                (object) ['active' => true],
            ];

            // Act
            $result = $rejectInactive($input);

            // Assert
            expect($result)->toHaveCount(2);
            expect($result[0]->active)->toBeTrue();
            expect($result[2]->active)->toBeTrue();
        });

        test('handles nested arrays', function (): void {
            // Arrange
            $rejectEmpty = reject(fn (array $arr): bool => $arr === []);
            $input = [[1, 2], [], [3], []];

            // Act
            $result = $rejectEmpty($input);

            // Assert
            expect($result)->toBe([0 => [1, 2], 2 => [3]]);
        });

        test('preserves numeric keys', function (): void {
            // Arrange
            $rejectEven = reject(fn (int $x): bool => $x % 2 === 0);
            $input = [10 => 1, 20 => 2, 30 => 3];

            // Act
            $result = $rejectEven($input);

            // Assert
            expect($result)->toBe([10 => 1, 30 => 3]);
        });

        test('handles mixed key types', function (): void {
            // Arrange
            $rejectEven = reject(fn (int $x): bool => $x % 2 === 0);
            $input = [0 => 1, 'key' => 2, 1 => 3, 'other' => 4];

            // Act
            $result = $rejectEven($input);

            // Assert
            expect($result)->toBe([0 => 1, 1 => 3]);
        });

        test('can be curried and reused', function (): void {
            // Arrange
            $rejectNegative = reject(fn (int $x): bool => $x < 0);

            // Act
            $result1 = $rejectNegative([1, -2, 3]);
            $result2 = $rejectNegative([-1, 2, -3]);

            // Assert
            expect($result1)->toBe([0 => 1, 2 => 3]);
            expect($result2)->toBe([1 => 2]);
        });

        test('works as opposite of filter', function (): void {
            // Arrange
            $isEven = fn (int $x): bool => $x % 2 === 0;
            $filterEven = filter($isEven);
            $rejectEven = reject($isEven);
            $input = [1, 2, 3, 4, 5, 6];

            // Act
            $filtered = $filterEven($input);
            $rejected = $rejectEven($input);

            // Assert
            expect(array_values($filtered))->toBe([2, 4, 6]);
            expect(array_values($rejected))->toBe([1, 3, 5]);
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
