<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use stdClass;

use function abs;
use function Cline\fp\uniqueBy;
use function describe;
use function expect;
use function spl_object_hash;
use function test;

describe('uniqueBy', function (): void {
    describe('Happy Paths', function (): void {
        test('removes duplicates by property value', function (): void {
            // Arrange
            $input = [
                ['id' => 1, 'name' => 'Alice'],
                ['id' => 2, 'name' => 'Bob'],
                ['id' => 1, 'name' => 'Charlie'],
                ['id' => 3, 'name' => 'David'],
            ];
            $keyFn = fn ($item) => $item['id'];

            // Act
            $result = uniqueBy($keyFn)($input);

            // Assert
            expect($result)->toBe([
                0 => ['id' => 1, 'name' => 'Alice'],
                1 => ['id' => 2, 'name' => 'Bob'],
                3 => ['id' => 3, 'name' => 'David'],
            ]);
        });

        test('removes duplicates by computed key', function (): void {
            // Arrange
            $input = [
                ['first' => 'John', 'last' => 'Doe'],
                ['first' => 'Jane', 'last' => 'Smith'],
                ['first' => 'John', 'last' => 'Smith'],
                ['first' => 'Jane', 'last' => 'Doe'],
            ];
            $keyFn = fn ($item): string => $item['first'].'|'.$item['last'];

            // Act
            $result = uniqueBy($keyFn)($input);

            // Assert
            expect($result)->toBe([
                0 => ['first' => 'John', 'last' => 'Doe'],
                1 => ['first' => 'Jane', 'last' => 'Smith'],
                2 => ['first' => 'John', 'last' => 'Smith'],
                3 => ['first' => 'Jane', 'last' => 'Doe'],
            ]);
        });

        test('preserves keys of first occurrence', function (): void {
            // Arrange
            $input = [
                'a' => ['type' => 'fruit'],
                'b' => ['type' => 'vegetable'],
                'c' => ['type' => 'fruit'],
                'd' => ['type' => 'grain'],
            ];
            $keyFn = fn ($item) => $item['type'];

            // Act
            $result = uniqueBy($keyFn)($input);

            // Assert
            expect($result)->toBe([
                'a' => ['type' => 'fruit'],
                'b' => ['type' => 'vegetable'],
                'd' => ['type' => 'grain'],
            ]);
        });

        test('returns empty array for empty input', function (): void {
            // Arrange
            $input = [];
            $keyFn = fn ($item) => $item;

            // Act
            $result = uniqueBy($keyFn)($input);

            // Assert
            expect($result)->toBe([]);
        });
    });

    describe('Edge Cases', function (): void {
        test('returns same array when all values unique by key function', function (): void {
            // Arrange
            $input = [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ];
            $keyFn = fn ($item) => $item['id'];

            // Act
            $result = uniqueBy($keyFn)($input);

            // Assert
            expect($result)->toBe([
                0 => ['id' => 1],
                1 => ['id' => 2],
                2 => ['id' => 3],
            ]);
        });

        test('returns single element when all values duplicate by key function', function (): void {
            // Arrange
            $input = [
                ['id' => 1, 'name' => 'Alice'],
                ['id' => 1, 'name' => 'Bob'],
                ['id' => 1, 'name' => 'Charlie'],
            ];
            $keyFn = fn ($item) => $item['id'];

            // Act
            $result = uniqueBy($keyFn)($input);

            // Assert
            expect($result)->toBe([0 => ['id' => 1, 'name' => 'Alice']]);
        });

        test('handles key function returning different types', function (): void {
            // Arrange
            $input = [
                ['value' => 1],
                ['value' => '1'],
                ['value' => true],
                ['value' => 1.0],
            ];
            $keyFn = fn ($item) => $item['value'];

            // Act
            $result = uniqueBy($keyFn)($input);

            // Assert - PHP isset() will coerce 1, '1', and true to same array key
            expect($result)->toBe([
                0 => ['value' => 1],
            ]);
        });

        test('handles key function returning null', function (): void {
            // Arrange
            $input = [
                ['name' => 'Alice', 'age' => 30],
                ['name' => 'Bob', 'age' => null],
                ['name' => 'Charlie', 'age' => null],
                ['name' => 'David', 'age' => 25],
            ];
            $keyFn = fn ($item) => $item['age'];

            // Act
            $result = uniqueBy($keyFn)($input);

            // Assert
            expect($result)->toBe([
                0 => ['name' => 'Alice', 'age' => 30],
                1 => ['name' => 'Bob', 'age' => null],
                3 => ['name' => 'David', 'age' => 25],
            ]);
        });

        test('handles key function returning object hashes', function (): void {
            // Arrange
            $obj1 = new stdClass();
            $obj1->id = 1;

            $obj2 = new stdClass();
            $obj2->id = 2;

            $input = [
                ['key' => $obj1, 'value' => 'A'],
                ['key' => $obj2, 'value' => 'B'],
                ['key' => $obj1, 'value' => 'C'],
            ];
            // Use spl_object_hash to get string key from object
            $keyFn = fn ($item): string => spl_object_hash($item['key']);

            // Act
            $result = uniqueBy($keyFn)($input);

            // Assert - Same object reference should be treated as duplicate
            expect($result)->toHaveCount(2);
            expect($result[0])->toBe(['key' => $obj1, 'value' => 'A']);
            expect($result[1])->toBe(['key' => $obj2, 'value' => 'B']);
        });

        test('handles case-sensitive string keys', function (): void {
            // Arrange
            $input = [
                ['name' => 'apple'],
                ['name' => 'Apple'],
                ['name' => 'APPLE'],
                ['name' => 'banana'],
            ];
            $keyFn = fn ($item) => $item['name'];

            // Act
            $result = uniqueBy($keyFn)($input);

            // Assert
            expect($result)->toBe([
                0 => ['name' => 'apple'],
                1 => ['name' => 'Apple'],
                2 => ['name' => 'APPLE'],
                3 => ['name' => 'banana'],
            ]);
        });

        test('handles key function with mathematical operations', function (): void {
            // Arrange
            $input = [1, -1, 2, -2, 3, 1, -3];
            $keyFn = fn ($num): float|int => abs($num);

            // Act
            $result = uniqueBy($keyFn)($input);

            // Assert
            expect($result)->toBe([0 => 1, 2 => 2, 4 => 3]);
        });
    });
});
