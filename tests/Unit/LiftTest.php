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

use function abs;
use function Cline\fp\lift;
use function Cline\fp\map;
use function describe;
use function expect;
use function mb_strlen;
use function mb_strtoupper;
use function test;

describe('lift()', function (): void {
    describe('Happy Paths', function (): void {
        test('lifts unary function into applicative context', function (): void {
            // Arrange
            $add1 = fn (int $x): int => $x + 1;
            $liftedAdd1 = lift($add1);
            $input = [1, 2, 3];

            // Act
            $result = $liftedAdd1($input);

            // Assert
            expect($result)->toBe([2, 3, 4]);
        });

        test('works as alias for map', function (): void {
            // Arrange
            $double = fn (int $x): int => $x * 2;
            $liftedDouble = lift($double);
            $mappedDouble = map($double);
            $input = [1, 2, 3, 4];

            // Act
            $liftResult = $liftedDouble($input);
            $mapResult = $mappedDouble($input);

            // Assert
            expect($liftResult)->toBe($mapResult);
            expect($liftResult)->toBe([2, 4, 6, 8]);
        });

        test('lifts string transformation function', function (): void {
            // Arrange
            $uppercase = fn (string $s): string => mb_strtoupper($s);
            $liftedUppercase = lift($uppercase);
            $input = ['hello', 'world'];

            // Act
            $result = $liftedUppercase($input);

            // Assert
            expect($result)->toBe(['HELLO', 'WORLD']);
        });

        test('lifts mathematical function', function (): void {
            // Arrange
            $square = fn (int $x): int => $x ** 2;
            $liftedSquare = lift($square);
            $input = [1, 2, 3, 4, 5];

            // Act
            $result = $liftedSquare($input);

            // Assert
            expect($result)->toBe([1, 4, 9, 16, 25]);
        });

        test('works with generator input', function (): void {
            // Arrange
            $triple = fn (int $x): int => $x * 3;
            $liftedTriple = lift($triple);
            $gen = function (): Generator {
                yield 1;

                yield 2;

                yield 3;
            };

            // Act
            $result = $liftedTriple($gen());

            // Assert
            expect($result)->toBe([3, 6, 9]);
        });

        test('works with ArrayIterator', function (): void {
            // Arrange
            $increment = fn (int $x): int => $x + 10;
            $liftedIncrement = lift($increment);
            $iterator = new ArrayIterator([5, 15, 25]);

            // Act
            $result = $liftedIncrement($iterator);

            // Assert
            expect($result)->toBe([15, 25, 35]);
        });

        test('demonstrates applicative functor pattern', function (): void {
            // Arrange
            $f = fn (int $x): int => $x + 5;
            $lifted = lift($f);
            $input = [10, 20, 30];

            // Act
            $result = $lifted($input);

            // Assert
            expect($result)->toBe([15, 25, 35]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: lift() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('handles empty array', function (): void {
            // Arrange
            $double = fn (int $x): int => $x * 2;
            $liftedDouble = lift($double);
            $input = [];

            // Act
            $result = $liftedDouble($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles empty generator', function (): void {
            // Arrange
            $increment = fn (int $x): int => $x + 1;
            $liftedIncrement = lift($increment);
            $gen = function (): Generator {
                return;

                yield;
            };

            // Act
            $result = $liftedIncrement($gen());

            // Assert
            expect($result)->toBe([]);
        });

        test('handles single element array', function (): void {
            // Arrange
            $square = fn (int $x): int => $x ** 2;
            $liftedSquare = lift($square);
            $input = [7];

            // Act
            $result = $liftedSquare($input);

            // Assert
            expect($result)->toBe([49]);
        });

        test('handles null values', function (): void {
            // Arrange
            $coalesce = fn (?int $x): int => $x ?? 0;
            $liftedCoalesce = lift($coalesce);
            $input = [1, null, 3];

            // Act
            $result = $liftedCoalesce($input);

            // Assert
            expect($result)->toBe([1, 0, 3]);
        });

        test('handles false values', function (): void {
            // Arrange
            $negate = fn (bool $b): bool => !$b;
            $liftedNegate = lift($negate);
            $input = [true, false, true];

            // Act
            $result = $liftedNegate($input);

            // Assert
            expect($result)->toBe([false, true, false]);
        });

        test('handles zero values', function (): void {
            // Arrange
            $reciprocal = fn (float $x): ?float => $x !== 0.0 ? 1.0 / $x : null;
            $liftedReciprocal = lift($reciprocal);
            $input = [2.0, 0.0, 4.0];

            // Act
            $result = $liftedReciprocal($input);

            // Assert
            expect($result)->toBe([0.5, null, 0.25]);
        });

        test('handles empty strings', function (): void {
            // Arrange
            $strlen = fn (string $s): int => mb_strlen($s);
            $liftedStrlen = lift($strlen);
            $input = ['hello', '', 'world'];

            // Act
            $result = $liftedStrlen($input);

            // Assert
            expect($result)->toBe([5, 0, 5]);
        });

        test('can be reused with different inputs', function (): void {
            // Arrange
            $abs = fn (int $x): int => abs($x);
            $liftedAbs = lift($abs);

            // Act
            $result1 = $liftedAbs([-1, -2, 3]);
            $result2 = $liftedAbs([4, -5, 6]);

            // Assert
            expect($result1)->toBe([1, 2, 3]);
            expect($result2)->toBe([4, 5, 6]);
        });

        test('lifts identity function', function (): void {
            // Arrange
            $identity = fn (mixed $x): mixed => $x;
            $liftedIdentity = lift($identity);
            $input = [1, 'hello', true, null];

            // Act
            $result = $liftedIdentity($input);

            // Assert
            expect($result)->toBe($input);
        });

        test('lifts constant function', function (): void {
            // Arrange
            $constant = fn (mixed $x): int => 42;
            $liftedConstant = lift($constant);
            $input = [1, 2, 3];

            // Act
            $result = $liftedConstant($input);

            // Assert
            expect($result)->toBe([42, 42, 42]);
        });

        test('works with type conversion functions', function (): void {
            // Arrange
            $toString = fn (mixed $x): string => (string) $x;
            $liftedToString = lift($toString);
            $input = [42, 3.14, true];

            // Act
            $result = $liftedToString($input);

            // Assert
            expect($result)->toBe(['42', '3.14', '1']);
        });

        test('preserves associative array values', function (): void {
            // Arrange
            $double = fn (int $x): int => $x * 2;
            $liftedDouble = lift($double);
            $input = ['a' => 10, 'b' => 20, 'c' => 30];

            // Act
            $result = $liftedDouble($input);

            // Assert
            expect($result)->toBe(['a' => 20, 'b' => 40, 'c' => 60]); // Keys preserved
        });

        test('lifts function returning arrays', function (): void {
            // Arrange
            $wrap = fn (int $x): array => [$x];
            $liftedWrap = lift($wrap);
            $input = [1, 2, 3];

            // Act
            $result = $liftedWrap($input);

            // Assert
            expect($result)->toBe([[1], [2], [3]]);
        });

        test('lifts function returning objects', function (): void {
            // Arrange
            $makeObj = fn (int $x): object => (object) ['value' => $x];
            $liftedMakeObj = lift($makeObj);
            $input = [5, 10];

            // Act
            $result = $liftedMakeObj($input);

            // Assert
            expect($result)->toHaveCount(2);
            expect($result[0]->value)->toBe(5);
            expect($result[1]->value)->toBe(10);
        });

        test('demonstrates functor law - identity', function (): void {
            // Arrange - fmap id = id
            $identity = fn (mixed $x): mixed => $x;
            $liftedIdentity = lift($identity);
            $input = [1, 2, 3];

            // Act
            $result = $liftedIdentity($input);

            // Assert
            expect($result)->toBe($input);
        });

        test('demonstrates functor law - composition', function (): void {
            // Arrange - fmap (g . f) = fmap g . fmap f
            $f = fn (int $x): int => $x + 1;
            $g = fn (int $x): int => $x * 2;
            $composed = fn (int $x): int => $g($f($x));

            $liftedComposed = lift($composed);
            $liftedF = lift($f);
            $liftedG = lift($g);

            $input = [1, 2, 3];

            // Act
            $result1 = $liftedComposed($input);
            $result2 = $liftedG($liftedF($input));

            // Assert
            expect($result1)->toBe($result2);
            expect($result1)->toBe([4, 6, 8]);
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
