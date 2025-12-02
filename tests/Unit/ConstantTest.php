<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\constant;
use function Cline\fp\map;
use function Cline\fp\pipe;
use function describe;
use function expect;
use function test;

describe('constant()', function (): void {
    describe('Happy Paths', function (): void {
        test('always returns same integer', function (): void {
            // Arrange
            $alwaysFortyTwo = constant(42);

            // Act & Assert
            expect($alwaysFortyTwo())->toBe(42);
            expect($alwaysFortyTwo(1, 2, 3))->toBe(42);
            expect($alwaysFortyTwo('ignored'))->toBe(42);
        });

        test('always returns same string', function (): void {
            // Arrange
            $alwaysHello = constant('hello');

            // Act & Assert
            expect($alwaysHello())->toBe('hello');
            expect($alwaysHello('world'))->toBe('hello');
        });

        test('always returns same array', function (): void {
            // Arrange
            $alwaysArray = constant([1, 2, 3]);

            // Act & Assert
            expect($alwaysArray())->toBe([1, 2, 3]);
            expect($alwaysArray('ignored'))->toBe([1, 2, 3]);
        });

        test('always returns same object', function (): void {
            // Arrange
            $obj = (object) ['id' => 1];
            $alwaysObj = constant($obj);

            // Act & Assert
            expect($alwaysObj())->toBe($obj);
            expect($alwaysObj('ignored'))->toBe($obj);
        });

        test('always returns same boolean', function (): void {
            // Arrange
            $alwaysTrue = constant(true);
            $alwaysFalse = constant(false);

            // Act & Assert
            expect($alwaysTrue())->toBeTrue();
            expect($alwaysTrue(1, 2, 3))->toBeTrue();
            expect($alwaysFalse())->toBeFalse();
        });

        test('useful for providing default values', function (): void {
            // Arrange
            $defaultUser = constant(['id' => 0, 'name' => 'Guest']);

            // Act
            $result = $defaultUser();

            // Assert
            expect($result)->toBe(['id' => 0, 'name' => 'Guest']);
        });

        test('ignores all arguments', function (): void {
            // Arrange
            $alwaysFive = constant(5);

            // Act & Assert
            expect($alwaysFive(100, 200, 300))->toBe(5);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: constant() accepts any value and creates a function that returns it
        // There are no true "sad paths" for this function
    });

    describe('Edge Cases', function (): void {
        test('always returns null', function (): void {
            // Arrange
            $alwaysNull = constant(null);

            // Act & Assert
            expect($alwaysNull())->toBeNull();
            expect($alwaysNull(1, 2, 3))->toBeNull();
        });

        test('always returns false', function (): void {
            // Arrange
            $alwaysFalse = constant(false);

            // Act & Assert
            expect($alwaysFalse())->toBeFalse();
            expect($alwaysFalse(true))->toBeFalse();
        });

        test('always returns zero', function (): void {
            // Arrange
            $alwaysZero = constant(0);

            // Act & Assert
            expect($alwaysZero())->toBe(0);
            expect($alwaysZero(100))->toBe(0);
        });

        test('always returns empty string', function (): void {
            // Arrange
            $alwaysEmpty = constant('');

            // Act & Assert
            expect($alwaysEmpty())->toBe('');
            expect($alwaysEmpty('hello'))->toBe('');
        });

        test('always returns empty array', function (): void {
            // Arrange
            $alwaysEmpty = constant([]);

            // Act & Assert
            expect($alwaysEmpty())->toBe([]);
            expect($alwaysEmpty([1, 2, 3]))->toBe([]);
        });

        test('works in map operation', function (): void {
            // Arrange
            $replaceWithZero = map(constant(0));
            $input = [1, 2, 3, 4, 5];

            // Act
            $result = $replaceWithZero($input);

            // Assert
            expect($result)->toBe([0, 0, 0, 0, 0]);
        });

        test('useful in conditional operations', function (): void {
            // Arrange
            $value = 5;
            $getFallback = constant('default');

            // Act
            $result = $value > 10 ? $value : $getFallback();

            // Assert
            expect($result)->toBe('default');
        });

        test('maintains object reference', function (): void {
            // Arrange
            $obj = (object) ['count' => 0];
            $alwaysObj = constant($obj);

            // Act
            $result1 = $alwaysObj();
            $result2 = $alwaysObj();
            $result1->count = 5;

            // Assert
            expect($result2->count)->toBe(5);
        });

        test('can wrap callable', function (): void {
            // Arrange
            $fn = fn (int $x): int => $x * 2;
            $alwaysFn = constant($fn);

            // Act
            $result = $alwaysFn();

            // Assert
            expect($result)->toBe($fn);
            expect($result(5))->toBe(10);
        });

        test('useful in composition', function (): void {
            // Arrange
            $pipeline = pipe(
                'ignored input',
                constant(42),
                fn (int $x): int => $x * 2,
            );

            // Act
            $result = $pipeline;

            // Assert
            expect($result)->toBe(84);
        });

        test('can be reused multiple times', function (): void {
            // Arrange
            $alwaysHello = constant('hello');

            // Act
            $result1 = $alwaysHello();
            $result2 = $alwaysHello(1, 2, 3);
            $result3 = $alwaysHello('world');

            // Assert
            expect($result1)->toBe('hello');
            expect($result2)->toBe('hello');
            expect($result3)->toBe('hello');
        });

        test('works with variadic arguments', function (): void {
            // Arrange
            $alwaysTen = constant(10);

            // Act
            $result = $alwaysTen(...[1, 2, 3, 4, 5]);

            // Assert
            expect($result)->toBe(10);
        });

        test('useful for stub functions in tests', function (): void {
            // Arrange
            $stubFunction = constant(['status' => 'success']);

            // Act
            $result = $stubFunction('any', 'arguments');

            // Assert
            expect($result)->toBe(['status' => 'success']);
        });

        test('creates independent constant functions', function (): void {
            // Arrange
            $alwaysOne = constant(1);
            $alwaysTwo = constant(2);

            // Act & Assert
            expect($alwaysOne())->toBe(1);
            expect($alwaysTwo())->toBe(2);
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
