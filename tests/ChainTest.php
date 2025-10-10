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
use function array_map;
use function Cline\fp\bind;
use function Cline\fp\chain;
use function Cline\fp\flatMap;
use function describe;
use function expect;
use function explode;
use function range;
use function test;

describe('chain()', function (): void {
    describe('Happy Paths', function (): void {
        test('maps and flattens array results', function (): void {
            // Arrange
            $duplicate = chain(fn (int $x): array => [$x, $x]);
            $input = [1, 2, 3];

            // Act
            $result = $duplicate($input);

            // Assert
            expect($result)->toBe([1, 1, 2, 2, 3, 3]);
        });

        test('works as alias for flatMap', function (): void {
            // Arrange
            $chainDuplicate = chain(fn (int $x): array => [$x, $x]);
            $flatMapDuplicate = flatMap(fn (int $x): array => [$x, $x]);
            $input = [1, 2, 3];

            // Act
            $chainResult = $chainDuplicate($input);
            $flatMapResult = $flatMapDuplicate($input);

            // Assert
            expect($chainResult)->toBe($flatMapResult);
            expect($chainResult)->toBe([1, 1, 2, 2, 3, 3]);
        });

        test('works as alias for bind', function (): void {
            // Arrange
            $chainDuplicate = chain(fn (int $x): array => [$x, $x]);
            $bindDuplicate = bind(fn (int $x): array => [$x, $x]);
            $input = [1, 2, 3];

            // Act
            $chainResult = $chainDuplicate($input);
            $bindResult = $bindDuplicate($input);

            // Assert
            expect($chainResult)->toBe($bindResult);
            expect($chainResult)->toBe([1, 1, 2, 2, 3, 3]);
        });

        test('explodes strings into words', function (): void {
            // Arrange
            $explodeWords = chain(fn (string $s): array => explode(' ', $s));
            $input = ['hello world', 'foo bar'];

            // Act
            $result = $explodeWords($input);

            // Assert
            expect($result)->toBe(['hello', 'world', 'foo', 'bar']);
        });

        test('chains multiple operations', function (): void {
            // Arrange
            $expand = chain(fn (int $x): array => range(1, $x));
            $input = [1, 2, 3];

            // Act
            $result = $expand($input);

            // Assert
            expect($result)->toBe([1, 1, 2, 1, 2, 3]);
        });

        test('demonstrates JavaScript-style chaining', function (): void {
            // Arrange
            $duplicate = chain(fn (int $x): array => [$x, $x]);
            $triple = chain(fn (int $x): array => [$x, $x, $x]);
            $input = [1, 2];

            // Act
            $result = $triple($duplicate($input));

            // Assert
            expect($result)->toBe([1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2]);
        });

        test('works with generator input', function (): void {
            // Arrange
            $duplicate = chain(fn (int $x): array => [$x, $x]);
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
            $duplicate = chain(fn (int $x): array => [$x, $x]);
            $iterator = new ArrayIterator([5, 10]);

            // Act
            $result = $duplicate($iterator);

            // Assert
            expect($result)->toBe([5, 5, 10, 10]);
        });

        test('flattens nested arrays one level', function (): void {
            // Arrange
            $identity = chain(fn (array $arr): array => $arr);
            $input = [[1, 2], [3, 4], [5, 6]];

            // Act
            $result = $identity($input);

            // Assert
            expect($result)->toBe([1, 2, 3, 4, 5, 6]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: chain() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('handles empty array', function (): void {
            // Arrange
            $duplicate = chain(fn (int $x): array => [$x, $x]);
            $input = [];

            // Act
            $result = $duplicate($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles function returning empty arrays', function (): void {
            // Arrange
            $returnEmpty = chain(fn (mixed $x): array => []);
            $input = [1, 2, 3];

            // Act
            $result = $returnEmpty($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles function returning single element arrays', function (): void {
            // Arrange
            $wrapInArray = chain(fn (int $x): array => [$x]);
            $input = [1, 2, 3];

            // Act
            $result = $wrapInArray($input);

            // Assert
            expect($result)->toBe([1, 2, 3]);
        });

        test('handles empty generator', function (): void {
            // Arrange
            $duplicate = chain(fn (int $x): array => [$x, $x]);
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
            $duplicate = chain(fn (int $x): array => [$x, $x]);
            $input = [42];

            // Act
            $result = $duplicate($input);

            // Assert
            expect($result)->toBe([42, 42]);
        });

        test('handles null values in mapped arrays', function (): void {
            // Arrange
            $withNull = chain(fn (int $x): array => [$x, null]);
            $input = [1, 2, 3];

            // Act
            $result = $withNull($input);

            // Assert
            expect($result)->toBe([1, null, 2, null, 3, null]);
        });

        test('handles false values in mapped arrays', function (): void {
            // Arrange
            $withFalse = chain(fn (int $x): array => [$x, false]);
            $input = [1, 2];

            // Act
            $result = $withFalse($input);

            // Assert
            expect($result)->toBe([1, false, 2, false]);
        });

        test('handles zero values in mapped arrays', function (): void {
            // Arrange
            $withZero = chain(fn (int $x): array => [$x, 0]);
            $input = [1, 2];

            // Act
            $result = $withZero($input);

            // Assert
            expect($result)->toBe([1, 0, 2, 0]);
        });

        test('handles empty strings in mapped arrays', function (): void {
            // Arrange
            $withEmpty = chain(fn (string $x): array => [$x, '']);
            $input = ['a', 'b'];

            // Act
            $result = $withEmpty($input);

            // Assert
            expect($result)->toBe(['a', '', 'b', '']);
        });

        test('does not preserve keys', function (): void {
            // Arrange
            $duplicate = chain(fn (string $v): array => [$v, $v]);
            $input = ['a' => '1', 'b' => '2'];

            // Act
            $result = $duplicate($input);

            // Assert
            expect($result)->toBe(['1', '1', '2', '2']);
            expect(array_keys($result))->toBe([0, 1, 2, 3]);
        });

        test('can be reused with different inputs', function (): void {
            // Arrange
            $duplicate = chain(fn (int $x): array => [$x, $x]);

            // Act
            $result1 = $duplicate([1, 2]);
            $result2 = $duplicate([3, 4, 5]);

            // Assert
            expect($result1)->toBe([1, 1, 2, 2]);
            expect($result2)->toBe([3, 3, 4, 4, 5, 5]);
        });

        test('useful for promise-like pattern', function (): void {
            // Arrange
            $safeDivide = fn (int $x): array => $x !== 0 ? [10 / $x] : [];
            $divideAll = chain($safeDivide);
            $input = [2, 0, 5];

            // Act
            $result = $divideAll($input);

            // Assert
            expect($result)->toBe([5, 2]); // Zero is filtered out
        });

        test('handles deeply nested arrays without deep flattening', function (): void {
            // Arrange
            $wrap = chain(fn (int $x): array => [[$x]]);
            $input = [1, 2, 3];

            // Act
            $result = $wrap($input);

            // Assert
            expect($result)->toBe([[1], [2], [3]]);
        });

        test('demonstrates JS Array.prototype.flatMap compatibility', function (): void {
            // Arrange - similar to JavaScript: arr.flatMap(x => [x, x * 2])
            $expandNumbers = chain(fn (int $x): array => [$x, $x * 2]);
            $input = [1, 2, 3];

            // Act
            $result = $expandNumbers($input);

            // Assert
            expect($result)->toBe([1, 2, 2, 4, 3, 6]);
        });

        test('chains conditional transformations', function (): void {
            // Arrange
            $expandEven = chain(fn (int $x): array => $x % 2 === 0 ? [$x, $x / 2] : [$x]);
            $input = [1, 2, 3, 4];

            // Act
            $result = $expandEven($input);

            // Assert
            expect($result)->toBe([1, 2, 1, 3, 4, 2]);
        });

        test('chains multiple operations fluently', function (): void {
            // Arrange
            $duplicate = chain(fn (int $x): array => [$x, $x]);
            $addOne = fn (array $arr): array => array_map(fn (int $x): int => $x + 1, $arr);
            $input = [1, 2];

            // Act
            $result = $addOne($duplicate($input));

            // Assert
            expect($result)->toBe([2, 2, 3, 3]);
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
