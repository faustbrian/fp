<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_keys;
use function Cline\fp\prepend;
use function describe;
use function expect;
use function test;

describe('prepend()', function (): void {
    describe('Happy Paths', function (): void {
        test('prepends value to numeric array', function (): void {
            // Arrange
            $prependZero = prepend(0);
            $input = [1, 2, 3];

            // Act
            $result = $prependZero($input);

            // Assert
            expect($result)->toBe([0, 1, 2, 3]);
        });

        test('prepends value to string array', function (): void {
            // Arrange
            $prependFirst = prepend('first');
            $input = ['second', 'third'];

            // Act
            $result = $prependFirst($input);

            // Assert
            expect($result)->toBe(['first', 'second', 'third']);
        });

        test('prepends value with key to array', function (): void {
            // Arrange
            $prependHeader = prepend('header', 'title');
            $input = ['a' => 1, 'b' => 2];

            // Act
            $result = $prependHeader($input);

            // Assert
            expect($result)->toBe(['title' => 'header', 'a' => 1, 'b' => 2]);
        });

        test('prepends value to empty array', function (): void {
            // Arrange
            $prependValue = prepend(42);
            $input = [];

            // Act
            $result = $prependValue($input);

            // Assert
            expect($result)->toBe([42]);
        });

        test('prepends value to associative array', function (): void {
            // Arrange
            $prependValue = prepend('new');
            $input = ['key1' => 'val1', 'key2' => 'val2'];

            // Act
            $result = $prependValue($input);

            // Assert
            expect($result)->toBe(['new', 'key1' => 'val1', 'key2' => 'val2']);
        });

        test('prepends value with numeric key', function (): void {
            // Arrange
            $prependValue = prepend('value', 0);
            $input = ['a', 'b', 'c'];

            // Act
            $result = $prependValue($input);

            // Assert
            expect($result)->toBe([0 => 'value', 1 => 'a', 2 => 'b', 3 => 'c']);
        });

        test('reindexes numeric keys after prepend', function (): void {
            // Arrange
            $prependValue = prepend('start');
            $input = [10 => 'a', 20 => 'b', 30 => 'c'];

            // Act
            $result = $prependValue($input);

            // Assert
            expect(array_keys($result))->toBe([0, 1, 2, 3]);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: prepend() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('prepends null value', function (): void {
            // Arrange
            $prependNull = prepend(null);
            $input = [1, 2, 3];

            // Act
            $result = $prependNull($input);

            // Assert
            expect($result)->toBe([null, 1, 2, 3]);
        });

        test('prepends false value', function (): void {
            // Arrange
            $prependFalse = prepend(false);
            $input = [true, true];

            // Act
            $result = $prependFalse($input);

            // Assert
            expect($result)->toBe([false, true, true]);
        });

        test('prepends zero value', function (): void {
            // Arrange
            $prependZero = prepend(0);
            $input = [1, 2, 3];

            // Act
            $result = $prependZero($input);

            // Assert
            expect($result)->toBe([0, 1, 2, 3]);
        });

        test('prepends empty string', function (): void {
            // Arrange
            $prependEmpty = prepend('');
            $input = ['a', 'b', 'c'];

            // Act
            $result = $prependEmpty($input);

            // Assert
            expect($result)->toBe(['', 'a', 'b', 'c']);
        });

        test('prepends array value', function (): void {
            // Arrange
            $prependArray = prepend([1, 2]);
            $input = [[3, 4], [5, 6]];

            // Act
            $result = $prependArray($input);

            // Assert
            expect($result)->toBe([[1, 2], [3, 4], [5, 6]]);
        });

        test('prepends object value', function (): void {
            // Arrange
            $obj = (object) ['id' => 1];
            $prependObj = prepend($obj);
            $input = [(object) ['id' => 2]];

            // Act
            $result = $prependObj($input);

            // Assert
            expect($result)->toHaveCount(2);
            expect($result[0])->toBe($obj);
        });

        test('prepends with string key overwrites existing key', function (): void {
            // Arrange
            $prependValue = prepend('new', 'key');
            $input = ['key' => 'old', 'other' => 'value'];

            // Act
            $result = $prependValue($input);

            // Assert
            expect($result)->toBe(['key' => 'new', 'other' => 'value']);
        });

        test('prepends to single element array', function (): void {
            // Arrange
            $prependValue = prepend('first');
            $input = ['second'];

            // Act
            $result = $prependValue($input);

            // Assert
            expect($result)->toBe(['first', 'second']);
        });

        test('does not mutate original array', function (): void {
            // Arrange
            $prependValue = prepend(0);
            $input = [1, 2, 3];
            $original = $input;

            // Act
            $result = $prependValue($input);

            // Assert
            expect($input)->toBe($original);
        });

        test('can be curried and reused', function (): void {
            // Arrange
            $prependZero = prepend(0);

            // Act
            $result1 = $prependZero([1, 2]);
            $result2 = $prependZero([3, 4]);

            // Assert
            expect($result1)->toBe([0, 1, 2]);
            expect($result2)->toBe([0, 3, 4]);
        });

        test('prepends with null key uses numeric index', function (): void {
            // Arrange
            $prependValue = prepend('value', null);
            $input = ['a', 'b'];

            // Act
            $result = $prependValue($input);

            // Assert
            expect($result)->toBe(['value', 'a', 'b']);
        });

        test('handles mixed key types in result', function (): void {
            // Arrange
            $prependValue = prepend('val', 'string-key');
            $input = [0 => 'a', 'other' => 'b', 1 => 'c'];

            // Act
            $result = $prependValue($input);

            // Assert
            expect($result)->toBe(['string-key' => 'val', 0 => 'a', 'other' => 'b', 1 => 'c']);
        });

        test('prepends special characters as key', function (): void {
            // Arrange
            $prependValue = prepend('value', 'key-with.special_chars');
            $input = ['a' => 1];

            // Act
            $result = $prependValue($input);

            // Assert
            expect($result)->toBe(['key-with.special_chars' => 'value', 'a' => 1]);
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
