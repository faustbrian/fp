<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function array_map;
use function array_merge;
use function array_product;
use function array_sum;
use function Cline\fp\apply;
use function Cline\fp\implode;
use function Cline\fp\map;
use function describe;
use function expect;
use function in_array;
use function sprintf;
use function test;

describe('apply()', function (): void {
    describe('Happy Paths', function (): void {
        test('applies function to array of arguments', function (): void {
            // Arrange
            $sum = fn (...$nums): int => array_sum($nums);
            $applySum = apply($sum);

            // Act
            $result = $applySum([1, 2, 3, 4]);

            // Assert
            expect($result)->toBe(10);
        });

        test('works with built-in max function', function (): void {
            // Arrange
            $applyMax = apply('max');

            // Act
            $result = $applyMax([5, 2, 8, 1, 9]);

            // Assert
            expect($result)->toBe(9);
        });

        test('works with built-in min function', function (): void {
            // Arrange
            $applyMin = apply('min');

            // Act
            $result = $applyMin([5, 2, 8, 1, 9]);

            // Assert
            expect($result)->toBe(1);
        });

        test('applies binary function', function (): void {
            // Arrange
            $add = fn (int $a, int $b): int => $a + $b;
            $applyAdd = apply($add);

            // Act
            $result = $applyAdd([5, 3]);

            // Assert
            expect($result)->toBe(8);
        });

        test('applies function with string concatenation', function (): void {
            // Arrange
            $concat = fn (string ...$strs): string => \implode(' ', $strs);
            $applyConcat = apply($concat);

            // Act
            $result = $applyConcat(['hello', 'world', 'foo']);

            // Assert
            expect($result)->toBe('hello world foo');
        });

        test('useful in map for applying to tuples', function (): void {
            // Arrange
            $add = fn (int $a, int $b): int => $a + $b;
            $mapper = map(apply($add));
            $input = [[1, 2], [3, 4], [5, 6]];

            // Act
            $result = $mapper($input);

            // Assert
            expect($result)->toBe([3, 7, 11]);
        });

        test('works with mixed type arguments', function (): void {
            // Arrange
            $format = fn (string $template, mixed ...$args): string => sprintf($template, ...$args);
            $applyFormat = apply($format);

            // Act
            $result = $applyFormat(['Name: %s, Age: %d', 'Alice', 25]);

            // Assert
            expect($result)->toBe('Name: Alice, Age: 25');
        });
    });

    describe('Sad Paths', function (): void {
        // Note: apply() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('applies function with no arguments', function (): void {
            // Arrange
            $getConstant = fn (): int => 42;
            $applyConstant = apply($getConstant);

            // Act
            $result = $applyConstant([]);

            // Assert
            expect($result)->toBe(42);
        });

        test('applies function with single argument', function (): void {
            // Arrange
            $double = fn (int $x): int => $x * 2;
            $applyDouble = apply($double);

            // Act
            $result = $applyDouble([21]);

            // Assert
            expect($result)->toBe(42);
        });

        test('applies function with many arguments', function (): void {
            // Arrange
            $sumAll = fn (...$nums): int => array_sum($nums);
            $applySum = apply($sumAll);

            // Act
            $result = $applySum([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

            // Assert
            expect($result)->toBe(55);
        });

        test('handles null in arguments', function (): void {
            // Arrange
            $coalesce = fn (?int $a, ?int $b, int $c): int => $a ?? $b ?? $c;
            $applyCoalesce = apply($coalesce);

            // Act
            $result = $applyCoalesce([null, null, 42]);

            // Assert
            expect($result)->toBe(42);
        });

        test('handles false in arguments', function (): void {
            // Arrange
            $allTrue = fn (bool ...$values): bool => !in_array(false, $values, true);
            $applyAll = apply($allTrue);

            // Act
            $result = $applyAll([true, false, true]);

            // Assert
            expect($result)->toBeFalse();
        });

        test('handles zero in arguments', function (): void {
            // Arrange
            $product = fn (...$nums): int => array_product($nums);
            $applyProduct = apply($product);

            // Act
            $result = $applyProduct([1, 2, 0, 4]);

            // Assert
            expect($result)->toBe(0);
        });

        test('handles empty string in arguments', function (): void {
            // Arrange
            $join = fn (string ...$strs): string => \implode('-', $strs);
            $applyJoin = apply($join);

            // Act
            $result = $applyJoin(['hello', '', 'world']);

            // Assert
            expect($result)->toBe('hello--world');
        });

        test('works with object methods', function (): void {
            // Arrange
            $obj = new class()
            {
                public function add(int $a, int $b): int
                {
                    return $a + $b;
                }
            };
            $applyAdd = apply($obj->add(...));

            // Act
            $result = $applyAdd([5, 3]);

            // Assert
            expect($result)->toBe(8);
        });

        test('can be curried and reused', function (): void {
            // Arrange
            $multiply = fn (int $a, int $b): int => $a * $b;
            $applyMultiply = apply($multiply);

            // Act
            $result1 = $applyMultiply([2, 3]);
            $result2 = $applyMultiply([4, 5]);

            // Assert
            expect($result1)->toBe(6);
            expect($result2)->toBe(20);
        });

        test('works in point-free style', function (): void {
            // Arrange
            $pairs = [[1, 2], [3, 4], [5, 6]];
            $sum = fn (int $a, int $b): int => $a + $b;

            // Act
            $result = array_map(apply($sum)(...), $pairs);

            // Assert
            expect($result)->toBe([3, 7, 11]);
        });

        test('handles arrays as arguments', function (): void {
            // Arrange
            $merge = fn (array $a, array $b): array => array_merge($a, $b);
            $applyMerge = apply($merge);

            // Act
            $result = $applyMerge([[1, 2], [3, 4]]);

            // Assert
            expect($result)->toBe([1, 2, 3, 4]);
        });

        test('handles objects as arguments', function (): void {
            // Arrange
            $combine = fn (object $a, object $b): int => $a->value + $b->value;
            $applyCombine = apply($combine);
            $obj1 = (object) ['value' => 10];
            $obj2 = (object) ['value' => 20];

            // Act
            $result = $applyCombine([$obj1, $obj2]);

            // Assert
            expect($result)->toBe(30);
        });

        test('works with closures', function (): void {
            // Arrange
            $makeClosure = fn (int $x): Closure => fn (int $y): int => $x + $y;
            $applyMakeClosure = apply($makeClosure);

            // Act
            $result = $applyMakeClosure([5]);

            // Assert
            expect($result(3))->toBe(8);
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
