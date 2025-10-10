<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\ap;
use function ctype_alpha;
use function describe;
use function expect;
use function mb_strlen;
use function mb_strtolower;
use function mb_strtoupper;
use function test;

describe('ap()', function (): void {
    describe('Happy Paths', function (): void {
        test('applies array of functions to array of values', function (): void {
            // Arrange
            $fns = [fn (int $x): int => $x + 1, fn (int $x): int => $x * 2];
            $applyFns = ap($fns);
            $input = [10, 20];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe([11, 21, 20, 40]);
        });

        test('creates Cartesian product of function applications', function (): void {
            // Arrange
            $fns = [fn (int $x): int => $x + 5, fn (int $x): int => $x - 5];
            $applyFns = ap($fns);
            $input = [10, 20, 30];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe([15, 25, 35, 5, 15, 25]);
        });

        test('applies single function to multiple values', function (): void {
            // Arrange
            $fns = [fn (int $x): int => $x * 3];
            $applyFns = ap($fns);
            $input = [1, 2, 3];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe([3, 6, 9]);
        });

        test('applies multiple functions to single value', function (): void {
            // Arrange
            $fns = [fn (int $x): int => $x + 1, fn (int $x): int => $x * 2, fn (int $x): int => $x ** 2];
            $applyFns = ap($fns);
            $input = [5];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe([6, 10, 25]);
        });

        test('works with string transformation functions', function (): void {
            // Arrange
            $fns = [fn (string $s): string => mb_strtoupper($s), fn (string $s): string => mb_strtolower($s)];
            $applyFns = ap($fns);
            $input = ['Hello', 'World'];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe(['HELLO', 'WORLD', 'hello', 'world']);
        });

        test('demonstrates applicative pattern', function (): void {
            // Arrange
            $double = fn (int $x): int => $x * 2;
            $triple = fn (int $x): int => $x * 3;
            $applyBoth = ap([$double, $triple]);
            $input = [2, 3];

            // Act
            $result = $applyBoth($input);

            // Assert
            expect($result)->toBe([4, 6, 6, 9]);
        });

        test('works with type casting functions', function (): void {
            // Arrange
            $fns = [fn (mixed $x): string => (string) $x, fn (mixed $x): int => (int) $x];
            $applyFns = ap($fns);
            $input = [42, 3.14];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe(['42', '3.14', 42, 3]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: ap() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns empty array when functions array is empty', function (): void {
            // Arrange
            $fns = [];
            $applyFns = ap($fns);
            $input = [1, 2, 3];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns empty array when values array is empty', function (): void {
            // Arrange
            $fns = [fn (int $x): int => $x + 1];
            $applyFns = ap($fns);
            $input = [];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns empty array when both arrays are empty', function (): void {
            // Arrange
            $fns = [];
            $applyFns = ap($fns);
            $input = [];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles single function and single value', function (): void {
            // Arrange
            $fns = [fn (int $x): int => $x + 10];
            $applyFns = ap($fns);
            $input = [5];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe([15]);
        });

        test('handles null values', function (): void {
            // Arrange
            $fns = [fn (?int $x): ?int => $x, fn (?int $x): int => $x ?? 0];
            $applyFns = ap($fns);
            $input = [null, 5];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe([null, 5, 0, 5]);
        });

        test('handles false values', function (): void {
            // Arrange
            $fns = [fn (bool $b): int => $b ? 1 : 0, fn (bool $b): string => $b ? 'true' : 'false'];
            $applyFns = ap($fns);
            $input = [true, false];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe([1, 0, 'true', 'false']);
        });

        test('handles zero values', function (): void {
            // Arrange
            $fns = [fn (int $x): int => $x + 1, fn (int $x): bool => $x === 0];
            $applyFns = ap($fns);
            $input = [0, 1];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe([1, 2, true, false]);
        });

        test('handles empty strings', function (): void {
            // Arrange
            $fns = [fn (string $s): int => mb_strlen($s), fn (string $s): bool => $s === ''];
            $applyFns = ap($fns);
            $input = ['', 'hello'];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe([0, 5, true, false]);
        });

        test('can be reused with different inputs', function (): void {
            // Arrange
            $fns = [fn (int $x): int => $x * 2];
            $applyFns = ap($fns);

            // Act
            $result1 = $applyFns([1, 2]);
            $result2 = $applyFns([3, 4, 5]);

            // Assert
            expect($result1)->toBe([2, 4]);
            expect($result2)->toBe([6, 8, 10]);
        });

        test('preserves order of function application', function (): void {
            // Arrange - functions applied in order, each to all values
            $fns = [fn (int $x): string => 'a'.$x, fn (int $x): string => 'b'.$x, fn (int $x): string => 'c'.$x];
            $applyFns = ap($fns);
            $input = [1, 2];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe(['a1', 'a2', 'b1', 'b2', 'c1', 'c2']);
        });

        test('works with complex return types', function (): void {
            // Arrange
            $fns = [fn (int $x): array => [$x], fn (int $x): array => [$x, $x]];
            $applyFns = ap($fns);
            $input = [1, 2];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toBe([[1], [2], [1, 1], [2, 2]]);
        });

        test('applies functions that return objects', function (): void {
            // Arrange
            $fns = [fn (int $x): object => (object) ['value' => $x], fn (int $x): object => (object) ['double' => $x * 2]];
            $applyFns = ap($fns);
            $input = [5, 10];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toHaveCount(4);
            expect($result[0]->value)->toBe(5);
            expect($result[3]->double)->toBe(20);
        });

        test('demonstrates applicative law - identity', function (): void {
            // Arrange - pure(id) <*> v = v
            $identity = fn (mixed $x): mixed => $x;
            $applyIdentity = ap([$identity]);
            $input = [1, 2, 3];

            // Act
            $result = $applyIdentity($input);

            // Assert
            expect($result)->toBe($input);
        });

        test('works with higher-order functions', function (): void {
            // Arrange
            $makeClosure = fn (int $x): callable => fn (int $y): int => $x + $y;
            $fns = [fn (int $x): callable => $makeClosure($x)];
            $applyFns = ap($fns);
            $input = [10, 20];

            // Act
            $result = $applyFns($input);

            // Assert
            expect($result)->toHaveCount(2);
            expect($result[0](5))->toBe(15);
            expect($result[1](5))->toBe(25);
        });

        test('useful for validation patterns', function (): void {
            // Arrange
            $validators = [
                fn (string $s): bool => mb_strlen($s) > 3,
                fn (string $s): bool => ctype_alpha($s),
                fn (string $s): bool => mb_strtolower($s) === $s,
            ];
            $applyValidators = ap($validators);
            $input = ['hello'];

            // Act
            $result = $applyValidators($input);

            // Assert
            expect($result)->toBe([true, true, true]);
        });

        test('combines with other applicative operations', function (): void {
            // Arrange
            $fns1 = [fn (int $x): int => $x + 1];
            $fns2 = [fn (int $x): int => $x * 2];
            $input = [5];

            // Act
            $step1 = ap($fns1)($input);
            $step2 = ap($fns2)($step1);

            // Assert
            expect($step1)->toBe([6]);
            expect($step2)->toBe([12]);
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
