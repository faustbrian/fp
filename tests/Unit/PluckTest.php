<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use ArrayIterator;
use stdClass;

use function Cline\fp\pluck;
use function describe;
use function expect;
use function test;

describe('pluck', function (): void {
    describe('Happy Paths', function (): void {
        test('plucks from array of arrays', function (): void {
            // Arrange
            $data = [
                ['id' => 1, 'name' => 'Alice'],
                ['id' => 2, 'name' => 'Bob'],
                ['id' => 3, 'name' => 'Charlie'],
            ];

            // Act
            $result = pluck('name')($data);

            // Assert
            expect($result)->toBe([0 => 'Alice', 1 => 'Bob', 2 => 'Charlie']);
        });

        test('plucks from array of objects', function (): void {
            // Arrange
            $obj1 = new class()
            {
                public string $id = '1';

                public string $name = 'Alice';
            };
            $obj2 = new class()
            {
                public string $id = '2';

                public string $name = 'Bob';
            };
            $obj3 = new class()
            {
                public string $id = '3';

                public string $name = 'Charlie';
            };
            $data = [$obj1, $obj2, $obj3];

            // Act
            $result = pluck('name')($data);

            // Assert
            expect($result)->toBe([0 => 'Alice', 1 => 'Bob', 2 => 'Charlie']);
        });

        test('plucks from array of stdClass objects', function (): void {
            // Arrange
            $obj1 = new stdClass();
            $obj1->id = 1;
            $obj1->name = 'Alice';

            $obj2 = new stdClass();
            $obj2->id = 2;
            $obj2->name = 'Bob';

            $obj3 = new stdClass();
            $obj3->id = 3;
            $obj3->name = 'Charlie';

            $data = [$obj1, $obj2, $obj3];

            // Act
            $result = pluck('name')($data);

            // Assert
            expect($result)->toBe([0 => 'Alice', 1 => 'Bob', 2 => 'Charlie']);
        });

        test('preserves keys from original array', function (): void {
            // Arrange
            $data = [
                'first' => ['id' => 1, 'name' => 'Alice'],
                'second' => ['id' => 2, 'name' => 'Bob'],
                'third' => ['id' => 3, 'name' => 'Charlie'],
            ];

            // Act
            $result = pluck('name')($data);

            // Assert
            expect($result)->toBe(['first' => 'Alice', 'second' => 'Bob', 'third' => 'Charlie']);
        });

        test('plucks numeric key from arrays', function (): void {
            // Arrange
            $data = [
                [0 => 'zero', 1 => 'one', 2 => 'two'],
                [0 => 'alpha', 1 => 'beta', 2 => 'gamma'],
                [0 => 'first', 1 => 'second', 2 => 'third'],
            ];

            // Act
            $result = pluck(1)($data);

            // Assert
            expect($result)->toBe([0 => 'one', 1 => 'beta', 2 => 'second']);
        });

        test('plucks from array with all same value', function (): void {
            // Arrange
            $data = [
                ['status' => 'active'],
                ['status' => 'active'],
                ['status' => 'active'],
            ];

            // Act
            $result = pluck('status')($data);

            // Assert
            expect($result)->toBe([0 => 'active', 1 => 'active', 2 => 'active']);
        });
    });

    describe('Sad Paths', function (): void {
        test('returns null for each element when plucking missing key', function (): void {
            // Arrange
            $data = [
                ['id' => 1, 'name' => 'Alice'],
                ['id' => 2, 'name' => 'Bob'],
                ['id' => 3, 'name' => 'Charlie'],
            ];

            // Act
            $result = pluck('email')($data);

            // Assert
            expect($result)->toBe([0 => null, 1 => null, 2 => null]);
        });

        test('returns empty array when plucking from empty array', function (): void {
            // Arrange
            $data = [];

            // Act
            $result = pluck('name')($data);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns null for missing object property', function (): void {
            // Arrange
            $obj1 = new stdClass();
            $obj1->id = 1;

            $obj2 = new stdClass();
            $obj2->id = 2;

            $data = [$obj1, $obj2];

            // Act
            $result = pluck('name')($data);

            // Assert
            expect($result)->toBe([0 => null, 1 => null]);
        });
    });

    describe('Edge Cases', function (): void {
        test('plucks from mixed arrays and objects', function (): void {
            // Arrange
            $obj = new stdClass();
            $obj->name = 'Object Alice';

            $data = [
                ['name' => 'Array Alice'],
                $obj,
                ['name' => 'Array Bob'],
            ];

            // Act
            $result = pluck('name')($data);

            // Assert
            expect($result)->toBe([0 => 'Array Alice', 1 => 'Object Alice', 2 => 'Array Bob']);
        });

        test('plucks with some elements missing the key', function (): void {
            // Arrange
            $data = [
                ['id' => 1, 'name' => 'Alice'],
                ['id' => 2],
                ['id' => 3, 'name' => 'Charlie'],
            ];

            // Act
            $result = pluck('name')($data);

            // Assert
            expect($result)->toBe([0 => 'Alice', 1 => null, 2 => 'Charlie']);
        });

        test('plucks from nested structures', function (): void {
            // Arrange
            $data = [
                ['user' => ['id' => 1, 'name' => 'Alice'], 'role' => 'admin'],
                ['user' => ['id' => 2, 'name' => 'Bob'], 'role' => 'user'],
                ['user' => ['id' => 3, 'name' => 'Charlie'], 'role' => 'guest'],
            ];

            // Act
            $result = pluck('user')($data);

            // Assert
            expect($result)->toBe([
                0 => ['id' => 1, 'name' => 'Alice'],
                1 => ['id' => 2, 'name' => 'Bob'],
                2 => ['id' => 3, 'name' => 'Charlie'],
            ]);
        });

        test('plucks with null values', function (): void {
            // Arrange
            $data = [
                ['id' => 1, 'name' => 'Alice'],
                ['id' => 2, 'name' => null],
                ['id' => 3, 'name' => 'Charlie'],
            ];

            // Act
            $result = pluck('name')($data);

            // Assert
            expect($result)->toBe([0 => 'Alice', 1 => null, 2 => 'Charlie']);
        });

        test('plucks with false values', function (): void {
            // Arrange
            $data = [
                ['id' => 1, 'active' => true],
                ['id' => 2, 'active' => false],
                ['id' => 3, 'active' => true],
            ];

            // Act
            $result = pluck('active')($data);

            // Assert
            expect($result)->toBe([0 => true, 1 => false, 2 => true]);
        });

        test('plucks with zero values', function (): void {
            // Arrange
            $data = [
                ['id' => 1, 'count' => 10],
                ['id' => 2, 'count' => 0],
                ['id' => 3, 'count' => 5],
            ];

            // Act
            $result = pluck('count')($data);

            // Assert
            expect($result)->toBe([0 => 10, 1 => 0, 2 => 5]);
        });

        test('plucks from ArrayIterator', function (): void {
            // Arrange
            $data = new ArrayIterator([
                ['id' => 1, 'name' => 'Alice'],
                ['id' => 2, 'name' => 'Bob'],
                ['id' => 3, 'name' => 'Charlie'],
            ]);

            // Act
            $result = pluck('name')($data);

            // Assert
            expect($result)->toBe([0 => 'Alice', 1 => 'Bob', 2 => 'Charlie']);
        });

        test('plucks from generator', function (): void {
            // Arrange
            $generator = function () {
                yield ['id' => 1, 'name' => 'Alice'];

                yield ['id' => 2, 'name' => 'Bob'];

                yield ['id' => 3, 'name' => 'Charlie'];
            };

            // Act
            $result = pluck('name')($generator());

            // Assert
            expect($result)->toBe([0 => 'Alice', 1 => 'Bob', 2 => 'Charlie']);
        });

        test('plucks with string numeric keys', function (): void {
            // Arrange
            $data = [
                ['0' => 'zero', '1' => 'one', '2' => 'two'],
                ['0' => 'alpha', '1' => 'beta', '2' => 'gamma'],
            ];

            // Act
            $result = pluck('1')($data);

            // Assert
            expect($result)->toBe([0 => 'one', 1 => 'beta']);
        });

        test('plucks empty string values', function (): void {
            // Arrange
            $data = [
                ['id' => 1, 'name' => 'Alice'],
                ['id' => 2, 'name' => ''],
                ['id' => 3, 'name' => 'Charlie'],
            ];

            // Act
            $result = pluck('name')($data);

            // Assert
            expect($result)->toBe([0 => 'Alice', 1 => '', 2 => 'Charlie']);
        });

        test('plucks from objects with magic __get method', function (): void {
            // Arrange
            $obj1 = new class()
            {
                private array $data = ['name' => 'Alice'];

                public function __get(string $key): mixed
                {
                    return $this->data[$key] ?? null;
                }
            };
            $obj2 = new class()
            {
                private array $data = ['name' => 'Bob'];

                public function __get(string $key): mixed
                {
                    return $this->data[$key] ?? null;
                }
            };
            $data = [$obj1, $obj2];

            // Act
            $result = pluck('name')($data);

            // Assert
            expect($result)->toBe([0 => 'Alice', 1 => 'Bob']);
        });

        test('plucks single element array', function (): void {
            // Arrange
            $data = [
                ['id' => 1, 'name' => 'Alice'],
            ];

            // Act
            $result = pluck('name')($data);

            // Assert
            expect($result)->toBe([0 => 'Alice']);
        });

        test('plucks from array with mixed value types', function (): void {
            // Arrange
            $data = [
                ['value' => 42],
                ['value' => 'string'],
                ['value' => [1, 2, 3]],
                ['value' => true],
                ['value' => null],
            ];

            // Act
            $result = pluck('value')($data);

            // Assert
            expect($result)->toBe([0 => 42, 1 => 'string', 2 => [1, 2, 3], 3 => true, 4 => null]);
        });

        test('plucks preserves associative array keys with gaps', function (): void {
            // Arrange
            $data = [
                10 => ['id' => 1, 'name' => 'Alice'],
                25 => ['id' => 2, 'name' => 'Bob'],
                50 => ['id' => 3, 'name' => 'Charlie'],
            ];

            // Act
            $result = pluck('name')($data);

            // Assert
            expect($result)->toBe([10 => 'Alice', 25 => 'Bob', 50 => 'Charlie']);
        });

        test('plucks with unicode property names', function (): void {
            // Arrange
            $data = [
                ['名前' => 'Alice', 'id' => 1],
                ['名前' => 'Bob', 'id' => 2],
            ];

            // Act
            $result = pluck('名前')($data);

            // Assert
            expect($result)->toBe([0 => 'Alice', 1 => 'Bob']);
        });

        test('plucks with unicode values', function (): void {
            // Arrange
            $data = [
                ['name' => '太郎'],
                ['name' => '花子'],
                ['name' => 'José'],
            ];

            // Act
            $result = pluck('name')($data);

            // Assert
            expect($result)->toBe([0 => '太郎', 1 => '花子', 2 => 'José']);
        });
    });
});
