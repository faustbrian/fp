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
use InvalidArgumentException;

use function Cline\fp\chunk;
use function describe;
use function expect;
use function test;

describe('chunk', function (): void {
    describe('Happy Paths', function (): void {
        test('chunks array evenly divisible by size', function (): void {
            $numbers = [1, 2, 3, 4, 5, 6];

            $result = chunk(2)($numbers);

            expect($result)->toBe([
                [0 => 1, 1 => 2],
                [2 => 3, 3 => 4],
                [4 => 5, 5 => 6],
            ]);
        });

        test('chunks array with remainder', function (): void {
            $numbers = [1, 2, 3, 4, 5];

            $result = chunk(2)($numbers);

            expect($result)->toBe([
                [0 => 1, 1 => 2],
                [2 => 3, 3 => 4],
                [4 => 5],
            ]);
        });

        test('chunks array when size is larger than array', function (): void {
            $numbers = [1, 2, 3];

            $result = chunk(10)($numbers);

            expect($result)->toBe([
                [0 => 1, 1 => 2, 2 => 3],
            ]);
        });

        test('preserves keys within chunks', function (): void {
            $data = ['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'];

            $result = chunk(2)($data);

            expect($result)->toBe([
                ['a' => 'A', 'b' => 'B'],
                ['c' => 'C', 'd' => 'D'],
            ]);
        });

        test('chunks with size of 3', function (): void {
            $numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9];

            $result = chunk(3)($numbers);

            expect($result)->toBe([
                [0 => 1, 1 => 2, 2 => 3],
                [3 => 4, 4 => 5, 5 => 6],
                [6 => 7, 7 => 8, 8 => 9],
            ]);
        });

        test('chunks iterator input', function (): void {
            $generator = function () {
                yield 1;

                yield 2;

                yield 3;

                yield 4;

                yield 5;
            };

            $result = chunk(2)($generator());

            expect($result)->toBe([
                [0 => 1, 1 => 2],
                [2 => 3, 3 => 4],
                [4 => 5],
            ]);
        });

        test('chunks iterator with custom keys', function (): void {
            $generator = function () {
                yield 'first' => 10;

                yield 'second' => 20;

                yield 'third' => 30;

                yield 'fourth' => 40;
            };

            $result = chunk(2)($generator());

            expect($result)->toBe([
                ['first' => 10, 'second' => 20],
                ['third' => 30, 'fourth' => 40],
            ]);
        });

        test('chunks ArrayIterator instance', function (): void {
            $array = [1, 2, 3, 4, 5, 6];
            $iterator = new ArrayIterator($array);

            $result = chunk(2)($iterator);

            expect($result)->toBe([
                [0 => 1, 1 => 2],
                [2 => 3, 3 => 4],
                [4 => 5, 5 => 6],
            ]);
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for size of zero', function (): void {
            $numbers = [1, 2, 3];

            expect(fn () => chunk(0)($numbers))
                ->toThrow(InvalidArgumentException::class, 'Chunk size must be greater than 0');
        });

        test('throws exception for negative size', function (): void {
            $numbers = [1, 2, 3];

            expect(fn () => chunk(-1)($numbers))
                ->toThrow(InvalidArgumentException::class, 'Chunk size must be greater than 0');
        });

        test('throws exception for large negative size', function (): void {
            $numbers = [1, 2, 3];

            expect(fn () => chunk(-100)($numbers))
                ->toThrow(InvalidArgumentException::class, 'Chunk size must be greater than 0');
        });
    });

    describe('Edge Cases', function (): void {
        test('chunks empty array returns empty array', function (): void {
            $empty = [];

            $result = chunk(3)($empty);

            expect($result)->toBe([]);
        });

        test('chunks with size of 1', function (): void {
            $numbers = [1, 2, 3];

            $result = chunk(1)($numbers);

            expect($result)->toBe([
                [0 => 1],
                [1 => 2],
                [2 => 3],
            ]);
        });

        test('chunks associative array preserves string keys', function (): void {
            $data = [
                'name' => 'Alice',
                'age' => 25,
                'city' => 'NYC',
                'country' => 'USA',
                'active' => true,
            ];

            $result = chunk(2)($data);

            expect($result)->toBe([
                ['name' => 'Alice', 'age' => 25],
                ['city' => 'NYC', 'country' => 'USA'],
                ['active' => true],
            ]);
        });

        test('chunks with mixed key types', function (): void {
            $data = [0 => 'zero', 'one' => 1, 2 => 'two', 'three' => 3];

            $result = chunk(2)($data);

            expect($result)->toBe([
                [0 => 'zero', 'one' => 1],
                [2 => 'two', 'three' => 3],
            ]);
        });

        test('chunks single element array', function (): void {
            $single = [42];

            $result = chunk(5)($single);

            expect($result)->toBe([
                [0 => 42],
            ]);
        });

        test('chunks array with size equal to array length', function (): void {
            $numbers = [1, 2, 3, 4, 5];

            $result = chunk(5)($numbers);

            expect($result)->toBe([
                [0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5],
            ]);
        });

        test('chunks array with very large size', function (): void {
            $numbers = [1, 2, 3];

            $result = chunk(1_000)($numbers);

            expect($result)->toBe([
                [0 => 1, 1 => 2, 2 => 3],
            ]);
        });

        test('chunks array with negative indices preserved', function (): void {
            $data = [-2 => 'neg2', -1 => 'neg1', 0 => 'zero', 1 => 'one'];

            $result = chunk(2)($data);

            expect($result)->toBe([
                [-2 => 'neg2', -1 => 'neg1'],
                [0 => 'zero', 1 => 'one'],
            ]);
        });

        test('chunks array containing null values', function (): void {
            $data = [1, null, 3, null, 5];

            $result = chunk(2)($data);

            expect($result)->toBe([
                [0 => 1, 1 => null],
                [2 => 3, 3 => null],
                [4 => 5],
            ]);
        });

        test('chunks array with complex objects', function (): void {
            $obj1 = new readonly class()
            {
                public function __construct(
                    public int $id = 1,
                ) {}
            };
            $obj2 = new readonly class()
            {
                public function __construct(
                    public int $id = 2,
                ) {}
            };
            $obj3 = new readonly class()
            {
                public function __construct(
                    public int $id = 3,
                ) {}
            };

            $objects = [$obj1, $obj2, $obj3];

            $result = chunk(2)($objects);

            expect($result)->toBe([
                [0 => $obj1, 1 => $obj2],
                [2 => $obj3],
            ]);
        });

        test('chunks nested arrays', function (): void {
            $data = [
                [1, 2],
                [3, 4],
                [5, 6],
                [7, 8],
            ];

            $result = chunk(2)($data);

            expect($result)->toBe([
                [0 => [1, 2], 1 => [3, 4]],
                [2 => [5, 6], 3 => [7, 8]],
            ]);
        });

        test('chunks empty iterator returns empty array', function (): void {
            $generator = function (): Generator {
                yield from [];
            };

            $result = chunk(3)($generator());

            expect($result)->toBe([]);
        });

        test('chunks with numeric string keys preserved', function (): void {
            $data = ['10' => 'ten', '20' => 'twenty', '30' => 'thirty'];

            $result = chunk(2)($data);

            expect($result)->toBe([
                ['10' => 'ten', '20' => 'twenty'],
                ['30' => 'thirty'],
            ]);
        });
    });
});
