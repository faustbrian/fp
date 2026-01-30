<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\map;
use function Cline\fp\sequence;
use function Cline\fp\traverse;
use function describe;
use function expect;
use function mb_strtolower;
use function mb_strtoupper;
use function range;
use function test;

describe('traverse()', function (): void {
    describe('Happy Paths', function (): void {
        test('maps then sequences wrapped values', function (): void {
            // Arrange
            $duplicate = fn (int $x): array => [$x, $x];
            $traverseDuplicate = traverse($duplicate);
            $input = [1, 2];

            // Act
            $result = $traverseDuplicate($input);

            // Assert
            expect($result)->toBe([[1, 2], [1, 2]]);
        });

        test('wraps each element then transposes', function (): void {
            // Arrange
            $wrap = fn (int $x): array => [$x];
            $traverseWrap = traverse($wrap);
            $input = [1, 2, 3];

            // Act
            $result = $traverseWrap($input);

            // Assert
            expect($result)->toBe([[1, 2, 3]]);
        });

        test('combines map and sequence operations', function (): void {
            // Arrange
            $triple = fn (int $x): array => [$x, $x * 2, $x * 3];
            $traverseTriple = traverse($triple);
            $input = [1, 2];

            // Act
            $result = $traverseTriple($input);

            // Assert
            expect($result)->toBe([[1, 2], [2, 4], [3, 6]]);
        });

        test('demonstrates traverse pattern with validation', function (): void {
            // Arrange
            $validate = fn (int $x): array => $x > 0 ? ['valid'] : ['invalid'];
            $traverseValidate = traverse($validate);
            $input = [1, 2, 3];

            // Act
            $result = $traverseValidate($input);

            // Assert
            expect($result)->toBe([['valid', 'valid', 'valid']]);
        });

        test('works with string transformations', function (): void {
            // Arrange
            $transformations = fn (string $s): array => [mb_strtoupper($s), mb_strtolower($s)];
            $traverseTransform = traverse($transformations);
            $input = ['Hello', 'World'];

            // Act
            $result = $traverseTransform($input);

            // Assert
            expect($result)->toBe([['HELLO', 'WORLD'], ['hello', 'world']]);
        });

        test('maps to pairs then sequences', function (): void {
            // Arrange
            $pair = fn (int $x): array => [$x, $x + 10];
            $traversePair = traverse($pair);
            $input = [1, 2, 3];

            // Act
            $result = $traversePair($input);

            // Assert
            expect($result)->toBe([[1, 2, 3], [11, 12, 13]]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: traverse() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('handles empty array', function (): void {
            // Arrange
            $duplicate = fn (int $x): array => [$x, $x];
            $traverseDuplicate = traverse($duplicate);
            $input = [];

            // Act
            $result = $traverseDuplicate($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles single element array', function (): void {
            // Arrange
            $duplicate = fn (int $x): array => [$x, $x];
            $traverseDuplicate = traverse($duplicate);
            $input = [5];

            // Act
            $result = $traverseDuplicate($input);

            // Assert
            expect($result)->toBe([[5], [5]]);
        });

        test('handles function returning empty arrays', function (): void {
            // Arrange
            $returnEmpty = fn (mixed $x): array => [];
            $traverseEmpty = traverse($returnEmpty);
            $input = [1, 2, 3];

            // Act
            $result = $traverseEmpty($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles function returning single element arrays', function (): void {
            // Arrange
            $wrapSingle = fn (int $x): array => [$x * 2];
            $traverseWrap = traverse($wrapSingle);
            $input = [1, 2, 3];

            // Act
            $result = $traverseWrap($input);

            // Assert
            expect($result)->toBe([[2, 4, 6]]);
        });

        test('handles ragged array results', function (): void {
            // Arrange
            $variableLength = fn (int $x): array => $x === 1 ? [1] : [2, 3];
            $traverseVariable = traverse($variableLength);
            $input = [1, 2];

            // Act
            $result = $traverseVariable($input);

            // Assert
            expect($result)->toBe([[1, 2], [3]]);
        });

        test('handles null values in mapped results', function (): void {
            // Arrange
            $withNull = fn (int $x): array => [$x, null];
            $traverseWithNull = traverse($withNull);
            $input = [1, 2];

            // Act
            $result = $traverseWithNull($input);

            // Assert
            expect($result)->toBe([[1, 2], [null, null]]);
        });

        test('handles false values in mapped results', function (): void {
            // Arrange
            $withFalse = fn (int $x): array => [$x > 1, false];
            $traverseWithFalse = traverse($withFalse);
            $input = [1, 2, 3];

            // Act
            $result = $traverseWithFalse($input);

            // Assert
            expect($result)->toBe([[false, true, true], [false, false, false]]);
        });

        test('handles zero values in mapped results', function (): void {
            // Arrange
            $withZero = fn (int $x): array => [$x, 0];
            $traverseWithZero = traverse($withZero);
            $input = [1, 2];

            // Act
            $result = $traverseWithZero($input);

            // Assert
            expect($result)->toBe([[1, 2], [0, 0]]);
        });

        test('handles empty strings in mapped results', function (): void {
            // Arrange
            $withEmpty = fn (string $x): array => [$x, ''];
            $traverseWithEmpty = traverse($withEmpty);
            $input = ['a', 'b'];

            // Act
            $result = $traverseWithEmpty($input);

            // Assert
            expect($result)->toBe([['a', 'b'], ['', '']]);
        });

        test('can be reused with different inputs', function (): void {
            // Arrange
            $duplicate = fn (int $x): array => [$x, $x];
            $traverseDuplicate = traverse($duplicate);

            // Act
            $result1 = $traverseDuplicate([1, 2]);
            $result2 = $traverseDuplicate([3, 4, 5]);

            // Assert
            expect($result1)->toBe([[1, 2], [1, 2]]);
            expect($result2)->toBe([[3, 4, 5], [3, 4, 5]]);
        });

        test('demonstrates map-then-sequence equivalence', function (): void {
            // Arrange
            $f = fn (int $x): array => [$x, $x * 10];
            $traverseF = traverse($f);
            $input = [1, 2, 3];

            // Act
            $traverseResult = $traverseF($input);
            $manualResult = sequence(map($f)($input));

            // Assert
            expect($traverseResult)->toBe($manualResult);
        });

        test('useful for option-like pattern with validation', function (): void {
            // Arrange
            $safeDivide = fn (int $x): array => $x !== 0 ? [10 / $x] : [];
            $traverseDivide = traverse($safeDivide);
            $input = [2, 5, 1];

            // Act
            $result = $traverseDivide($input);

            // Assert
            expect($result)->toBe([[5, 2, 10]]);
        });

        test('filters out empty results', function (): void {
            // Arrange
            $conditional = fn (int $x): array => $x % 2 === 0 ? [$x] : [];
            $traverseConditional = traverse($conditional);
            $input = [1, 2, 3, 4, 5];

            // Act
            $result = $traverseConditional($input);

            // Assert
            expect($result)->toBe([[2, 4]]);
        });

        test('works with three possible outcomes', function (): void {
            // Arrange
            $categorize = fn (int $x): array => [
                $x < 0 ? 'negative' : ($x === 0 ? 'zero' : 'positive'),
            ];
            $traverseCategorize = traverse($categorize);
            $input = [-1, 0, 1];

            // Act
            $result = $traverseCategorize($input);

            // Assert
            expect($result)->toBe([['negative', 'zero', 'positive']]);
        });

        test('applies complex transformation', function (): void {
            // Arrange
            $transform = fn (int $x): array => [$x * 2, $x * 3, $x * 4];
            $traverseTransform = traverse($transform);
            $input = [1, 2];

            // Act
            $result = $traverseTransform($input);

            // Assert
            expect($result)->toBe([[2, 4], [3, 6], [4, 8]]);
        });

        test('works with objects in mapped results', function (): void {
            // Arrange
            $makeObj = fn (int $x): array => [(object) ['value' => $x], (object) ['value' => $x * 2]];
            $traverseMakeObj = traverse($makeObj);
            $input = [1, 2];

            // Act
            $result = $traverseMakeObj($input);

            // Assert
            expect($result)->toHaveCount(2);
            expect($result[0])->toHaveCount(2);
            expect($result[0][0]->value)->toBe(1);
            expect($result[1][1]->value)->toBe(4);
        });

        test('demonstrates effectful computation pattern', function (): void {
            // Arrange - simulating validation that returns success/failure paths
            $validate = fn (int $x): array => $x > 0 && $x < 10 ? ['valid'] : ['invalid'];
            $traverseValidate = traverse($validate);
            $input = [1, 2, 3];

            // Act
            $result = $traverseValidate($input);

            // Assert
            expect($result)->toBe([['valid', 'valid', 'valid']]);
        });

        test('inverts structure correctly', function (): void {
            // Arrange
            $expand = fn (int $x): array => range(1, $x);
            $traverseExpand = traverse($expand);
            $input = [2, 3];

            // Act
            $result = $traverseExpand($input);

            // Assert
            expect($result)->toBe([[1, 1], [2, 2], [3]]);
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
