<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_sum;
use function Cline\fp\juxt;
use function count;
use function ctype_alpha;
use function describe;
use function expect;
use function mb_strlen;
use function mb_strtolower;
use function mb_strtoupper;
use function reset;
use function strrev;
use function test;

describe('juxt()', function (): void {
    describe('Happy Paths', function (): void {
        test('applies multiple functions to same value', function (): void {
            // Arrange
            $stats = juxt(
                fn (array $arr): int => count($arr),
                fn (array $arr): int => array_sum($arr),
                fn (array $arr): float => array_sum($arr) / count($arr),
            );
            $input = [1, 2, 3, 4];

            // Act
            $result = $stats($input);

            // Assert
            expect($result)->toBe([4, 10, 2.5]);
        });

        test('extracts multiple properties from object', function (): void {
            // Arrange
            $userInfo = juxt(
                fn (object $u): string => $u->name,
                fn (object $u): string => $u->email,
                fn (object $u): int => $u->age,
            );
            $user = (object) ['name' => 'Alice', 'email' => 'alice@example.com', 'age' => 25];

            // Act
            $result = $userInfo($user);

            // Assert
            expect($result)->toBe(['Alice', 'alice@example.com', 25]);
        });

        test('applies transformations to number', function (): void {
            // Arrange
            $transforms = juxt(
                fn (int $x): int => $x * 2,
                fn (int $x): int => $x + 10,
                fn (int $x): int => $x ** 2,
            );

            // Act
            $result = $transforms(5);

            // Assert
            expect($result)->toBe([10, 15, 25]);
        });

        test('applies string operations', function (): void {
            // Arrange
            $stringOps = juxt(
                fn (string $s): int => mb_strlen($s),
                fn (string $s): string => mb_strtoupper($s),
                fn (string $s): string => strrev($s),
            );

            // Act
            $result = $stringOps('hello');

            // Assert
            expect($result)->toBe([5, 'HELLO', 'olleh']);
        });

        test('works with single function', function (): void {
            // Arrange
            $singleFn = juxt(fn (int $x): int => $x * 2);

            // Act
            $result = $singleFn(5);

            // Assert
            expect($result)->toBe([10]);
        });

        test('works with many functions', function (): void {
            // Arrange
            $manyFns = juxt(
                fn (int $x): int => $x,
                fn (int $x): int => $x * 2,
                fn (int $x): int => $x * 3,
                fn (int $x): int => $x * 4,
                fn (int $x): int => $x * 5,
            );

            // Act
            $result = $manyFns(10);

            // Assert
            expect($result)->toBe([10, 20, 30, 40, 50]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: juxt() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('handles functions returning null', function (): void {
            // Arrange
            $fns = juxt(
                fn (mixed $x): ?int => null,
                fn (mixed $x): int => 42,
            );

            // Act
            $result = $fns('input');

            // Assert
            expect($result)->toBe([null, 42]);
        });

        test('handles functions returning false', function (): void {
            // Arrange
            $fns = juxt(
                fn (mixed $x): bool => false,
                fn (mixed $x): bool => true,
            );

            // Act
            $result = $fns('input');

            // Assert
            expect($result)->toBe([false, true]);
        });

        test('handles functions returning zero', function (): void {
            // Arrange
            $fns = juxt(
                fn (int $x): int => 0,
                fn (int $x): int => $x,
            );

            // Act
            $result = $fns(5);

            // Assert
            expect($result)->toBe([0, 5]);
        });

        test('handles functions returning empty values', function (): void {
            // Arrange
            $fns = juxt(
                fn (mixed $x): string => '',
                fn (mixed $x): array => [],
                fn (mixed $x): ?int => null,
            );

            // Act
            $result = $fns('input');

            // Assert
            expect($result)->toBe(['', [], null]);
        });

        test('applies to null input', function (): void {
            // Arrange
            $fns = juxt(
                fn (?int $x): bool => $x === null,
                fn (?int $x): int => $x ?? 0,
            );

            // Act
            $result = $fns(null);

            // Assert
            expect($result)->toBe([true, 0]);
        });

        test('applies to array input', function (): void {
            // Arrange
            $fns = juxt(
                fn (array $arr): int => count($arr),
                fn (array $arr): bool => $arr === [],
                fn (array $arr): mixed => $arr !== [] ? reset($arr) : null,
            );

            // Act
            $result = $fns([1, 2, 3]);

            // Assert
            expect($result)->toBe([3, false, 1]);
        });

        test('applies to object input', function (): void {
            // Arrange
            $obj = (object) ['x' => 10, 'y' => 20];
            $fns = juxt(
                fn (object $o): int => $o->x,
                fn (object $o): int => $o->y,
                fn (object $o): int => $o->x + $o->y,
            );

            // Act
            $result = $fns($obj);

            // Assert
            expect($result)->toBe([10, 20, 30]);
        });

        test('can be reused with different inputs', function (): void {
            // Arrange
            $fns = juxt(
                fn (int $x): int => $x * 2,
                fn (int $x): int => $x + 1,
            );

            // Act
            $result1 = $fns(5);
            $result2 = $fns(10);

            // Assert
            expect($result1)->toBe([10, 6]);
            expect($result2)->toBe([20, 11]);
        });

        test('works with closures that capture variables', function (): void {
            // Arrange
            $multiplier = 3;
            $fns = juxt(
                fn (int $x): int => $x * $multiplier,
                fn (int $x): int => $x + 1,
            );

            // Act
            $result = $fns(5);

            // Assert
            expect($result)->toBe([15, 6]);
        });

        test('preserves order of functions', function (): void {
            // Arrange
            $fns = juxt(
                fn (int $x): string => 'first',
                fn (int $x): string => 'second',
                fn (int $x): string => 'third',
            );

            // Act
            $result = $fns(0);

            // Assert
            expect($result)->toBe(['first', 'second', 'third']);
        });

        test('functions can return different types', function (): void {
            // Arrange
            $fns = juxt(
                fn (int $x): int => $x,
                fn (int $x): string => (string) $x,
                fn (int $x): float => (float) $x,
                fn (int $x): bool => $x > 0,
                fn (int $x): array => [$x],
            );

            // Act
            $result = $fns(42);

            // Assert
            expect($result)->toBe([42, '42', 42.0, true, [42]]);
        });

        test('useful for validation checks', function (): void {
            // Arrange
            $validators = juxt(
                fn (string $s): bool => mb_strlen($s) > 3,
                fn (string $s): bool => ctype_alpha($s),
                fn (string $s): bool => $s === mb_strtolower($s),
            );

            // Act
            $result = $validators('hello');

            // Assert
            expect($result)->toBe([true, true, true]);
        });

        test('works in functional composition', function (): void {
            // Arrange
            $analyze = juxt(
                fn (array $arr): int => count($arr),
                fn (array $arr): int => array_sum($arr),
            );
            $input = [1, 2, 3, 4, 5];

            // Act
            [$count, $sum] = $analyze($input);

            // Assert
            expect($count)->toBe(5);
            expect($sum)->toBe(15);
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
