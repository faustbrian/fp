<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_merge;
use function Cline\fp\flip;
use function describe;
use function expect;
use function test;

describe('flip()', function (): void {
    describe('Happy Paths', function (): void {
        test('flips arguments for subtraction', function (): void {
            // Arrange
            $subtract = fn (int $a, int $b): int => $a - $b;
            $flippedSubtract = flip($subtract);

            // Act
            $result = $flippedSubtract(10, 3);

            // Assert
            expect($result)->toBe(-7); // Same as subtract(3, 10)
        });

        test('flips arguments for division', function (): void {
            // Arrange
            $divide = fn (float $a, float $b): float => $a / $b;
            $flippedDivide = flip($divide);

            // Act
            $result = $flippedDivide(2, 10);

            // Assert
            expect($result)->toBe(5.0); // Same as divide(10, 2)
        });

        test('flips arguments for string concatenation', function (): void {
            // Arrange
            $concat = fn (string $a, string $b): string => $a.$b;
            $flippedConcat = flip($concat);

            // Act
            $result = $flippedConcat('world', 'hello ');

            // Assert
            expect($result)->toBe('hello world');
        });

        test('enables partial application from right', function (): void {
            // Arrange
            $divide = fn (float $a, float $b): float => $a / $b;
            $divideBy2 = flip($divide)(2);

            // Act
            $result = $divideBy2(10);

            // Assert
            expect($result)->toBe(5.0); // Same as divide(10, 2)
        });

        test('works with array functions', function (): void {
            // Arrange
            $append = fn (array $arr, mixed $val): array => [...$arr, $val];
            $flippedAppend = flip($append);

            // Act
            $result = $flippedAppend(4, [1, 2, 3]);

            // Assert
            expect($result)->toBe([1, 2, 3, 4]);
        });

        test('flips comparison operations', function (): void {
            // Arrange
            $greaterThan = fn (int $a, int $b): bool => $a > $b;
            $lessThan = flip($greaterThan);

            // Act
            $result = $lessThan(3, 5);

            // Assert
            expect($result)->toBeTrue(); // Same as greaterThan(5, 3)
        });
    });

    describe('Sad Paths', function (): void {
        // Note: flip() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('works with mixed type arguments', function (): void {
            // Arrange
            $mixedFn = fn (int $a, string $b): string => $b.'-'.$a;
            $flipped = flip($mixedFn);

            // Act
            $result = $flipped('hello', 42);

            // Assert
            expect($result)->toBe('hello-42');
        });

        test('handles null arguments', function (): void {
            // Arrange
            $fn = fn (?int $a, ?int $b): ?int => $a ?? $b;
            $flipped = flip($fn);

            // Act
            $result = $flipped(null, 5);

            // Assert
            expect($result)->toBe(5);
        });

        test('handles false arguments', function (): void {
            // Arrange
            $fn = fn (bool $a, bool $b): bool => $a && $b;
            $flipped = flip($fn);

            // Act
            $result = $flipped(false, true);

            // Assert
            expect($result)->toBeFalse();
        });

        test('handles zero arguments', function (): void {
            // Arrange
            $fn = fn (int $a, int $b): int => $a + $b;
            $flipped = flip($fn);

            // Act
            $result = $flipped(0, 5);

            // Assert
            expect($result)->toBe(5);
        });

        test('handles empty string arguments', function (): void {
            // Arrange
            $fn = fn (string $a, string $b): string => $a.$b;
            $flipped = flip($fn);

            // Act
            $result = $flipped('', 'hello');

            // Assert
            expect($result)->toBe('hello');
        });

        test('flipping twice returns original behavior', function (): void {
            // Arrange
            $subtract = fn (int $a, int $b): int => $a - $b;
            $flippedTwice = flip(flip($subtract));

            // Act
            $result = $flippedTwice(10, 3);

            // Assert
            expect($result)->toBe(7); // Same as original subtract(10, 3)
        });

        test('works with object arguments', function (): void {
            // Arrange
            $fn = fn (object $a, object $b): int => $a->value + $b->value;
            $flipped = flip($fn);
            $obj1 = (object) ['value' => 5];
            $obj2 = (object) ['value' => 10];

            // Act
            $result = $flipped($obj1, $obj2);

            // Assert
            expect($result)->toBe(15);
        });

        test('works with array arguments', function (): void {
            // Arrange
            $fn = fn (array $a, array $b): array => array_merge($a, $b);
            $flipped = flip($fn);

            // Act
            $result = $flipped([3, 4], [1, 2]);

            // Assert
            expect($result)->toBe([1, 2, 3, 4]);
        });

        test('can be used in composition', function (): void {
            // Arrange
            $subtract = fn (int $a, int $b): int => $a - $b;
            $flipped = flip($subtract);
            $double = fn (int $x): int => $x * 2;

            // Act
            $result = $double($flipped(5, 10)); // double(subtract(10, 5))

            // Assert
            expect($result)->toBe(10);
        });

        test('can be curried multiple times', function (): void {
            // Arrange
            $power = fn (int $base, int $exp): int => $base ** $exp;
            $flippedPower = flip($power);
            $square = $flippedPower(2);
            $cube = $flippedPower(3);

            // Act
            $squared = $square(5);
            $cubed = $cube(5);

            // Assert
            expect($squared)->toBe(25); // 5 ** 2
            expect($cubed)->toBe(125);  // 5 ** 3
        });

        test('handles callable string', function (): void {
            // Arrange
            $flipped = flip('pow');

            // Act
            $result = $flipped(2, 10);

            // Assert
            expect($result)->toBe(100); // pow(10, 2)
        });

        test('useful for point-free style', function (): void {
            // Arrange
            $append = fn (string $str, string $suffix): string => $str.$suffix;
            $addExclamation = flip($append)('!');

            // Act
            $result1 = $addExclamation('Hello');
            $result2 = $addExclamation('World');

            // Assert
            expect($result1)->toBe('Hello!');
            expect($result2)->toBe('World!');
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
