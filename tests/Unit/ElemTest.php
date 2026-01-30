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

use function Cline\fp\contains;
use function Cline\fp\elem;
use function describe;
use function expect;
use function test;

describe('elem()', function (): void {
    describe('Happy Paths', function (): void {
        test('finds integer in array', function (): void {
            // Arrange
            $hasThree = elem(3);
            $input = [1, 2, 3, 4];

            // Act
            $result = $hasThree($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('returns false when integer not found', function (): void {
            // Arrange
            $hasFive = elem(5);
            $input = [1, 2, 3, 4];

            // Act
            $result = $hasFive($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('finds string in array', function (): void {
            // Arrange
            $hasApple = elem('apple');
            $input = ['orange', 'banana', 'apple'];

            // Act
            $result = $hasApple($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('works as alias for contains', function (): void {
            // Arrange
            $elemHasThree = elem(3);
            $containsThree = contains(3);
            $input = [1, 2, 3, 4];

            // Act
            $elemResult = $elemHasThree($input);
            $containsResult = $containsThree($input);

            // Assert
            expect($elemResult)->toBe($containsResult);
            expect($elemResult)->toBeTrue();
        });

        test('works with generator', function (): void {
            // Arrange
            $hasThree = elem(3);
            $gen = function (): Generator {
                yield 1;

                yield 2;

                yield 3;

                yield 4;
            };

            // Act
            $result = $hasThree($gen());

            // Assert
            expect($result)->toBeTrue();
        });

        test('works with ArrayIterator', function (): void {
            // Arrange
            $hasTen = elem(10);
            $iterator = new ArrayIterator([5, 10, 15]);

            // Act
            $result = $hasTen($iterator);

            // Assert
            expect($result)->toBeTrue();
        });

        test('uses strict comparison', function (): void {
            // Arrange
            $hasStringThree = elem('3');
            $input = [1, 2, 3, 4];

            // Act
            $result = $hasStringThree($input);

            // Assert
            expect($result)->toBeFalse();
        });
    });

    describe('Sad Paths', function (): void {
        // Note: elem() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns false for empty array', function (): void {
            // Arrange
            $hasValue = elem(1);
            $input = [];

            // Act
            $result = $hasValue($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('returns false for empty generator', function (): void {
            // Arrange
            $hasValue = elem(1);
            $gen = function (): Generator {
                return;

                yield;
            };

            // Act
            $result = $hasValue($gen());

            // Assert
            expect($result)->toBeFalse();
        });

        test('finds null value', function (): void {
            // Arrange
            $hasNull = elem(null);
            $input = [1, null, 3];

            // Act
            $result = $hasNull($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('finds false value', function (): void {
            // Arrange
            $hasFalse = elem(false);
            $input = [true, false, true];

            // Act
            $result = $hasFalse($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('distinguishes false from 0', function (): void {
            // Arrange
            $hasFalse = elem(false);
            $input = [0, 1, 2];

            // Act
            $result = $hasFalse($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('finds zero value', function (): void {
            // Arrange
            $hasZero = elem(0);
            $input = [1, 0, 2];

            // Act
            $result = $hasZero($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('distinguishes 0 from empty string', function (): void {
            // Arrange
            $hasZero = elem(0);
            $input = ['', 'a', 'b'];

            // Act
            $result = $hasZero($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('finds empty string', function (): void {
            // Arrange
            $hasEmpty = elem('');
            $input = ['a', '', 'b'];

            // Act
            $result = $hasEmpty($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('finds value in single element array', function (): void {
            // Arrange
            $hasValue = elem(42);
            $input = [42];

            // Act
            $result = $hasValue($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('does not find value in single element array', function (): void {
            // Arrange
            $hasValue = elem(42);
            $input = [41];

            // Act
            $result = $hasValue($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('works with associative arrays', function (): void {
            // Arrange
            $hasValue = elem('bar');
            $input = ['foo' => 'bar', 'baz' => 'qux'];

            // Act
            $result = $hasValue($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('does not find keys only values', function (): void {
            // Arrange
            $hasKey = elem('foo');
            $input = ['foo' => 'bar'];

            // Act
            $result = $hasKey($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('finds nested array by value', function (): void {
            // Arrange
            $needle = [1, 2];
            $hasArray = elem($needle);
            $input = [[1, 2], [3, 4]];

            // Act
            $result = $hasArray($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('finds object by reference', function (): void {
            // Arrange
            $obj = (object) ['id' => 1];
            $hasObj = elem($obj);
            $input = [$obj, (object) ['id' => 2]];

            // Act
            $result = $hasObj($input);

            // Assert
            expect($result)->toBeTrue();
        });

        test('does not match similar objects', function (): void {
            // Arrange
            $obj1 = (object) ['id' => 1];
            $obj2 = (object) ['id' => 1];
            $hasObj = elem($obj1);
            $input = [$obj2];

            // Act
            $result = $hasObj($input);

            // Assert
            expect($result)->toBeFalse();
        });

        test('can be reused with different arrays', function (): void {
            // Arrange
            $hasThree = elem(3);

            // Act
            $result1 = $hasThree([1, 2, 3]);
            $result2 = $hasThree([4, 5, 6]);

            // Assert
            expect($result1)->toBeTrue();
            expect($result2)->toBeFalse();
        });

        test('stops searching after finding value', function (): void {
            // Arrange
            $count = 0;
            $gen = function () use (&$count): Generator {
                yield ++$count;

                yield ++$count;

                yield ++$count;

                yield ++$count;
            };
            $hasTwo = elem(2);

            // Act
            $result = $hasTwo($gen());

            // Assert
            expect($result)->toBeTrue();
            expect($count)->toBe(2); // Stopped after finding 2
        });

        test('useful in Haskell-style list operations', function (): void {
            // Arrange
            $validStatuses = ['active', 'pending', 'completed'];
            $isValidStatus = elem('active');

            // Act
            $result = $isValidStatus($validStatuses);

            // Assert
            expect($result)->toBeTrue();
        });

        test('demonstrates Haskell naming convention', function (): void {
            // Arrange
            $list = [1, 2, 3, 4, 5];

            // Act
            $has3 = elem(3)($list);
            $has6 = elem(6)($list);

            // Assert
            expect($has3)->toBeTrue();
            expect($has6)->toBeFalse();
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
