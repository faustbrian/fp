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

use function array_keys;
use function array_values;
use function Cline\fp\flatMap;
use function describe;
use function expect;
use function explode;
use function range;
use function test;

describe('flatMap()', function (): void {
    describe('Happy Paths', function (): void {
        test('maps and flattens array results', function (): void {
            // Arrange
            $duplicate = flatMap(fn (int $x): array => [$x, $x]);
            $input = [1, 2, 3];

            // Act
            $result = $duplicate($input);

            // Assert
            expect($result)->toBe([1, 1, 2, 2, 3, 3]);
        });

        test('explodes strings into words', function (): void {
            // Arrange
            $explodeWords = flatMap(fn (string $s): array => explode(' ', $s));
            $input = ['hello world', 'foo bar'];

            // Act
            $result = $explodeWords($input);

            // Assert
            expect($result)->toBe(['hello', 'world', 'foo', 'bar']);
        });

        test('maps to arrays of different lengths', function (): void {
            // Arrange
            $expand = flatMap(fn (int $x): array => range(1, $x));
            $input = [1, 2, 3];

            // Act
            $result = $expand($input);

            // Assert
            expect($result)->toBe([1, 1, 2, 1, 2, 3]);
        });

        test('works with generator input', function (): void {
            // Arrange
            $duplicate = flatMap(fn (int $x): array => [$x, $x]);
            $gen = function (): Generator {
                yield 1;

                yield 2;

                yield 3;
            };

            // Act
            $result = $duplicate($gen());

            // Assert
            expect($result)->toBe([1, 1, 2, 2, 3, 3]);
        });

        test('works with ArrayIterator', function (): void {
            // Arrange
            $duplicate = flatMap(fn (int $x): array => [$x, $x]);
            $iterator = new ArrayIterator([5, 10]);

            // Act
            $result = $duplicate($iterator);

            // Assert
            expect($result)->toBe([5, 5, 10, 10]);
        });

        test('flattens nested arrays one level', function (): void {
            // Arrange
            $expand = flatMap(fn (array $arr): array => $arr);
            $input = [[1, 2], [3, 4], [5, 6]];

            // Act
            $result = $expand($input);

            // Assert
            expect($result)->toBe([1, 2, 3, 4, 5, 6]);
        });

        test('does not preserve keys', function (): void {
            // Arrange
            $duplicate = flatMap(fn (string $v): array => [$v, $v]);
            $input = ['a' => '1', 'b' => '2'];

            // Act
            $result = $duplicate($input);

            // Assert
            expect($result)->toBe(['1', '1', '2', '2']);
            expect(array_keys($result))->toBe([0, 1, 2, 3]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: flatMap() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('handles empty array', function (): void {
            // Arrange
            $duplicate = flatMap(fn (int $x): array => [$x, $x]);
            $input = [];

            // Act
            $result = $duplicate($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles mapper returning empty arrays', function (): void {
            // Arrange
            $returnEmpty = flatMap(fn (mixed $x): array => []);
            $input = [1, 2, 3];

            // Act
            $result = $returnEmpty($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles mapper returning single element arrays', function (): void {
            // Arrange
            $wrapInArray = flatMap(fn (int $x): array => [$x]);
            $input = [1, 2, 3];

            // Act
            $result = $wrapInArray($input);

            // Assert
            expect($result)->toBe([1, 2, 3]);
        });

        test('handles mapper returning non-array for some elements', function (): void {
            // Arrange
            $conditional = flatMap(fn (int $x): mixed => $x % 2 === 0 ? [$x, $x] : $x);
            $input = [1, 2, 3, 4];

            // Act
            $result = $conditional($input);

            // Assert
            expect($result)->toBe([1, 2, 2, 3, 4, 4]);
        });

        test('handles empty generator', function (): void {
            // Arrange
            $duplicate = flatMap(fn (int $x): array => [$x, $x]);
            $gen = function (): Generator {
                return;

                yield;
            };

            // Act
            $result = $duplicate($gen());

            // Assert
            expect($result)->toBe([]);
        });

        test('handles single element array', function (): void {
            // Arrange
            $duplicate = flatMap(fn (int $x): array => [$x, $x]);
            $input = [42];

            // Act
            $result = $duplicate($input);

            // Assert
            expect($result)->toBe([42, 42]);
        });

        test('handles mixed types in mapped arrays', function (): void {
            // Arrange
            $expand = flatMap(fn (int $x): array => [$x, (string) $x, (float) $x]);
            $input = [1, 2];

            // Act
            $result = $expand($input);

            // Assert
            expect($result)->toBe([1, '1', 1.0, 2, '2', 2.0]);
        });

        test('handles null values in mapped arrays', function (): void {
            // Arrange
            $withNull = flatMap(fn (int $x): array => [$x, null]);
            $input = [1, 2, 3];

            // Act
            $result = $withNull($input);

            // Assert
            expect($result)->toBe([1, null, 2, null, 3, null]);
        });

        test('handles false values in mapped arrays', function (): void {
            // Arrange
            $withFalse = flatMap(fn (int $x): array => [$x, false]);
            $input = [1, 2];

            // Act
            $result = $withFalse($input);

            // Assert
            expect($result)->toBe([1, false, 2, false]);
        });

        test('handles zero values in mapped arrays', function (): void {
            // Arrange
            $withZero = flatMap(fn (int $x): array => [$x, 0]);
            $input = [1, 2];

            // Act
            $result = $withZero($input);

            // Assert
            expect($result)->toBe([1, 0, 2, 0]);
        });

        test('handles empty strings in mapped arrays', function (): void {
            // Arrange
            $withEmpty = flatMap(fn (string $x): array => [$x, '']);
            $input = ['a', 'b'];

            // Act
            $result = $withEmpty($input);

            // Assert
            expect($result)->toBe(['a', '', 'b', '']);
        });

        test('handles deeply nested arrays without deep flattening', function (): void {
            // Arrange
            $wrap = flatMap(fn (int $x): array => [[$x]]);
            $input = [1, 2, 3];

            // Act
            $result = $wrap($input);

            // Assert
            expect($result)->toBe([[1], [2], [3]]);
        });

        test('handles objects in mapped arrays', function (): void {
            // Arrange
            $obj1 = (object) ['id' => 1];
            $obj2 = (object) ['id' => 2];
            $wrapObj = flatMap(fn (object $obj): array => [$obj, $obj]);
            $input = [$obj1, $obj2];

            // Act
            $result = $wrapObj($input);

            // Assert
            expect($result)->toHaveCount(4);
            expect($result[0])->toBe($obj1);
            expect($result[1])->toBe($obj1);
        });

        test('chains multiple flatMap operations', function (): void {
            // Arrange
            $duplicate = flatMap(fn (int $x): array => [$x, $x]);
            $triple = flatMap(fn (int $x): array => [$x, $x, $x]);
            $input = [1, 2];

            // Act
            $result = $triple($duplicate($input));

            // Assert
            expect($result)->toBe([1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2]);
        });

        test('works with associative array values', function (): void {
            // Arrange
            $extractValues = flatMap(fn (array $arr): array => array_values($arr));
            $input = [['a' => 1, 'b' => 2], ['c' => 3, 'd' => 4]];

            // Act
            $result = $extractValues($input);

            // Assert
            expect($result)->toBe([1, 2, 3, 4]);
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
