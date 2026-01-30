<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_values;
use function Cline\fp\compose;
use function Cline\fp\filter;
use function Cline\fp\identity;
use function Cline\fp\map;
use function Cline\fp\pipe;
use function describe;
use function expect;
use function fclose;
use function fopen;
use function test;

describe('identity()', function (): void {
    describe('Happy Paths', function (): void {
        test('returns integer unchanged', function (): void {
            // Arrange & Act
            $result = identity(42);

            // Assert
            expect($result)->toBe(42);
        });

        test('returns string unchanged', function (): void {
            // Arrange & Act
            $result = identity('hello');

            // Assert
            expect($result)->toBe('hello');
        });

        test('returns array unchanged', function (): void {
            // Arrange
            $input = [1, 2, 3];

            // Act
            $result = identity($input);

            // Assert
            expect($result)->toBe($input);
        });

        test('returns object unchanged', function (): void {
            // Arrange
            $obj = (object) ['id' => 1, 'name' => 'Test'];

            // Act
            $result = identity($obj);

            // Assert
            expect($result)->toBe($obj);
        });

        test('returns float unchanged', function (): void {
            // Arrange & Act
            $result = identity(3.14);

            // Assert
            expect($result)->toBe(3.14);
        });

        test('returns boolean unchanged', function (): void {
            // Arrange & Act
            $result = identity(true);

            // Assert
            expect($result)->toBeTrue();
        });

        test('useful as default mapper', function (): void {
            // Arrange
            $mapper = map(identity(...));
            $input = [1, 2, 3];

            // Act
            $result = $mapper($input);

            // Assert
            expect($result)->toBe($input);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: identity() accepts any type and simply returns it
        // There are no true "sad paths" for this function
    });

    describe('Edge Cases', function (): void {
        test('returns null unchanged', function (): void {
            // Arrange & Act
            $result = identity(null);

            // Assert
            expect($result)->toBeNull();
        });

        test('returns false unchanged', function (): void {
            // Arrange & Act
            $result = identity(false);

            // Assert
            expect($result)->toBeFalse();
        });

        test('returns zero unchanged', function (): void {
            // Arrange & Act
            $result = identity(0);

            // Assert
            expect($result)->toBe(0);
        });

        test('returns empty string unchanged', function (): void {
            // Arrange & Act
            $result = identity('');

            // Assert
            expect($result)->toBe('');
        });

        test('returns empty array unchanged', function (): void {
            // Arrange
            $input = [];

            // Act
            $result = identity($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns nested array unchanged', function (): void {
            // Arrange
            $input = [[1, 2], [3, 4]];

            // Act
            $result = identity($input);

            // Assert
            expect($result)->toBe($input);
        });

        test('returns associative array unchanged', function (): void {
            // Arrange
            $input = ['key' => 'value', 'foo' => 'bar'];

            // Act
            $result = identity($input);

            // Assert
            expect($result)->toBe($input);
        });

        test('returns callable unchanged', function (): void {
            // Arrange
            $fn = fn (int $x): int => $x * 2;

            // Act
            $result = identity($fn);

            // Assert
            expect($result)->toBe($fn);
            expect($result(5))->toBe(10);
        });

        test('works in function composition', function (): void {
            // Arrange
            $double = fn (int $x): int => $x * 2;
            $composed = compose(identity(...), $double);

            // Act
            $result = $composed(5);

            // Assert
            expect($result)->toBe(10);
        });

        test('works in pipe', function (): void {
            // Arrange
            $input = 42;

            // Act
            $result = pipe(
                $input,
                identity(...),
                fn (int $x): int => $x * 2,
                identity(...),
            );

            // Assert
            expect($result)->toBe(84);
        });

        test('useful for extracting values without transformation', function (): void {
            // Arrange
            $filter = filter(identity(...));
            $input = [0, 1, false, 'text', null, '', 42];

            // Act
            $result = $filter($input);

            // Assert
            expect(array_values($result))->toBe([1, 'text', 42]);
        });

        test('returns resource type unchanged', function (): void {
            // Arrange
            $resource = fopen('php://memory', 'rb');

            // Act
            $result = identity($resource);

            // Assert
            expect($result)->toBe($resource);
            fclose($resource);
        });

        test('maintains object reference', function (): void {
            // Arrange
            $obj = (object) ['count' => 0];

            // Act
            $result = identity($obj);
            $result->count = 5;

            // Assert
            expect($obj->count)->toBe(5);
        });

        test('maintains array reference for objects', function (): void {
            // Arrange
            $array = ['nested' => ['value' => 1]];

            // Act
            $result = identity($array);

            // Assert
            expect($result)->toBe($array);
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
