<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use ArrayIterator;

use function abs;
use function Cline\fp\sortBy;
use function count;
use function describe;
use function expect;
use function mb_strlen;
use function mb_strtolower;
use function test;

describe('sortBy', function (): void {
    describe('Happy Paths', function (): void {
        test('sorts by numeric property', function (): void {
            $users = [
                ['name' => 'Alice', 'age' => 30],
                ['name' => 'Bob', 'age' => 25],
                ['name' => 'Charlie', 'age' => 35],
            ];
            $getAge = fn (array $user): int => $user['age'];

            $result = sortBy($getAge)($users);

            expect($result)->toBe([
                1 => ['name' => 'Bob', 'age' => 25],
                0 => ['name' => 'Alice', 'age' => 30],
                2 => ['name' => 'Charlie', 'age' => 35],
            ]);
        });

        test('sorts by string property', function (): void {
            $users = [
                ['name' => 'Charlie', 'age' => 35],
                ['name' => 'Alice', 'age' => 30],
                ['name' => 'Bob', 'age' => 25],
            ];
            $getName = fn (array $user): string => $user['name'];

            $result = sortBy($getName)($users);

            expect($result)->toBe([
                1 => ['name' => 'Alice', 'age' => 30],
                2 => ['name' => 'Bob', 'age' => 25],
                0 => ['name' => 'Charlie', 'age' => 35],
            ]);
        });

        test('sorts by computed value', function (): void {
            $numbers = [3, 1, 4, 1, 5, 9, 2, 6];
            $negateValue = fn (int $x): int => -$x; // Sort descending

            $result = sortBy($negateValue)($numbers);

            expect($result)->toBe([5 => 9, 7 => 6, 4 => 5, 2 => 4, 0 => 3, 6 => 2, 1 => 1, 3 => 1]);
        });

        test('preserves keys during sort', function (): void {
            $data = ['a' => 3, 'b' => 1, 'c' => 4, 'd' => 2];
            $identity = fn (int $x): int => $x;

            $result = sortBy($identity)($data);

            expect($result)->toBe(['b' => 1, 'd' => 2, 'a' => 3, 'c' => 4]);
        });

        test('sorts by absolute value', function (): void {
            $numbers = [-3, 5, -1, 0, 2, -5];
            $absoluteValue = fn (int $x): int => abs($x);

            $result = sortBy($absoluteValue)($numbers);

            expect($result)->toBe([3 => 0, 2 => -1, 4 => 2, 0 => -3, 1 => 5, 5 => -5]);
        });

        test('sorts strings by length', function (): void {
            $words = ['a', 'abc', 'ab', 'abcd'];
            $getLength = fn (string $word): int => mb_strlen($word);

            $result = sortBy($getLength)($words);

            expect($result)->toBe([0 => 'a', 2 => 'ab', 1 => 'abc', 3 => 'abcd']);
        });
    });

    describe('Sad Paths', function (): void {
        test('handles already sorted array', function (): void {
            $sorted = [1, 2, 3, 4, 5];
            $identity = fn (int $x): int => $x;

            $result = sortBy($identity)($sorted);

            expect($result)->toBe([1, 2, 3, 4, 5]);
        });

        test('handles reverse sorted array', function (): void {
            $reverseSorted = [5, 4, 3, 2, 1];
            $identity = fn (int $x): int => $x;

            $result = sortBy($identity)($reverseSorted);

            expect($result)->toBe([4 => 1, 3 => 2, 2 => 3, 1 => 4, 0 => 5]);
        });

        test('handles all identical values', function (): void {
            $identical = [5, 5, 5, 5, 5];
            $identity = fn (int $x): int => $x;

            $result = sortBy($identity)($identical);

            expect($result)->toBe([5, 5, 5, 5, 5]);
        });

        test('handles all items sorting to same key value', function (): void {
            $data = [1, 2, 3, 4, 5];
            $constantKey = fn (int $x): int => 0; // All map to same value

            $result = sortBy($constantKey)($data);

            expect($result)->toBe([1, 2, 3, 4, 5]);
        });
    });

    describe('Edge Cases', function (): void {
        test('handles empty array', function (): void {
            $empty = [];
            $identity = fn (int $x): int => $x;

            $result = sortBy($identity)($empty);

            expect($result)->toBe([]);
        });

        test('handles single element', function (): void {
            $single = [42];
            $identity = fn (int $x): int => $x;

            $result = sortBy($identity)($single);

            expect($result)->toBe([42]);
        });

        test('sorts iterator input', function (): void {
            $generator = function () {
                yield 'c' => 30;

                yield 'a' => 10;

                yield 'd' => 40;

                yield 'b' => 20;
            };
            $identity = fn (int $x): int => $x;

            $result = sortBy($identity)($generator());

            expect($result)->toBe(['a' => 10, 'b' => 20, 'c' => 30, 'd' => 40]);
        });

        test('sorts ArrayIterator instance', function (): void {
            $array = [5, 2, 8, 1, 9];
            $iterator = new ArrayIterator($array);
            $identity = fn (int $x): int => $x;

            $result = sortBy($identity)($iterator);

            expect($result)->toBe([3 => 1, 1 => 2, 0 => 5, 2 => 8, 4 => 9]);
        });

        test('sorts objects by property', function (): void {
            $item1 = new readonly class()
            {
                public function __construct(
                    public string $name = 'Charlie',
                    public int $value = 30,
                ) {}
            };
            $item2 = new readonly class()
            {
                public function __construct(
                    public string $name = 'Alice',
                    public int $value = 10,
                ) {}
            };
            $item3 = new readonly class()
            {
                public function __construct(
                    public string $name = 'Bob',
                    public int $value = 20,
                ) {}
            };

            $items = [$item1, $item2, $item3];
            $getValue = fn (object $item): int => $item->value;

            $result = sortBy($getValue)($items);

            expect($result)->toBe([1 => $item2, 2 => $item3, 0 => $item1]);
        });

        test('sorts by case-insensitive string comparison', function (): void {
            $words = ['Delta', 'alpha', 'CHARLIE', 'Bravo'];
            $toLowerCase = fn (string $word): string => mb_strtolower($word);

            $result = sortBy($toLowerCase)($words);

            expect($result)->toBe([1 => 'alpha', 3 => 'Bravo', 2 => 'CHARLIE', 0 => 'Delta']);
        });

        test('sorts by nested property', function (): void {
            $data = [
                ['user' => ['age' => 30], 'id' => 1],
                ['user' => ['age' => 20], 'id' => 2],
                ['user' => ['age' => 25], 'id' => 3],
            ];
            $getNestedAge = fn (array $item): int => $item['user']['age'];

            $result = sortBy($getNestedAge)($data);

            expect($result)->toBe([
                1 => ['user' => ['age' => 20], 'id' => 2],
                2 => ['user' => ['age' => 25], 'id' => 3],
                0 => ['user' => ['age' => 30], 'id' => 1],
            ]);
        });

        test('sorts by boolean value', function (): void {
            $data = [
                ['name' => 'Alice', 'active' => true],
                ['name' => 'Bob', 'active' => false],
                ['name' => 'Charlie', 'active' => true],
                ['name' => 'David', 'active' => false],
            ];
            $getActive = fn (array $item): bool => $item['active'];

            $result = sortBy($getActive)($data);

            // false (0) comes before true (1)
            expect($result)->toBe([
                1 => ['name' => 'Bob', 'active' => false],
                3 => ['name' => 'David', 'active' => false],
                0 => ['name' => 'Alice', 'active' => true],
                2 => ['name' => 'Charlie', 'active' => true],
            ]);
        });

        test('sorts by computed score', function (): void {
            $students = [
                ['name' => 'Alice', 'math' => 80, 'english' => 90],
                ['name' => 'Bob', 'math' => 95, 'english' => 75],
                ['name' => 'Charlie', 'math' => 70, 'english' => 85],
            ];
            $getAverage = fn (array $student): float => ($student['math'] + $student['english']) / 2;

            $result = sortBy($getAverage)($students);

            expect($result)->toBe([
                2 => ['name' => 'Charlie', 'math' => 70, 'english' => 85],
                0 => ['name' => 'Alice', 'math' => 80, 'english' => 90],
                1 => ['name' => 'Bob', 'math' => 95, 'english' => 75],
            ]);
        });

        test('sorts with nullable values', function (): void {
            $data = [
                ['name' => 'Alice', 'score' => 100],
                ['name' => 'Bob', 'score' => null],
                ['name' => 'Charlie', 'score' => 50],
                ['name' => 'David', 'score' => null],
            ];
            $getScore = fn (array $item): ?int => $item['score'];

            $result = sortBy($getScore)($data);

            // null values sort before numeric values with spaceship operator
            expect($result)->toBe([
                1 => ['name' => 'Bob', 'score' => null],
                3 => ['name' => 'David', 'score' => null],
                2 => ['name' => 'Charlie', 'score' => 50],
                0 => ['name' => 'Alice', 'score' => 100],
            ]);
        });

        test('sorts by date strings', function (): void {
            $events = [
                ['name' => 'Event C', 'date' => '2024-03-15'],
                ['name' => 'Event A', 'date' => '2024-01-10'],
                ['name' => 'Event B', 'date' => '2024-02-20'],
            ];
            $getDate = fn (array $event): string => $event['date'];

            $result = sortBy($getDate)($events);

            expect($result)->toBe([
                1 => ['name' => 'Event A', 'date' => '2024-01-10'],
                2 => ['name' => 'Event B', 'date' => '2024-02-20'],
                0 => ['name' => 'Event C', 'date' => '2024-03-15'],
            ]);
        });

        test('sorts by array count', function (): void {
            $data = [
                'a' => [1, 2, 3, 4],
                'b' => [1],
                'c' => [1, 2, 3],
                'd' => [1, 2],
            ];
            $getCount = fn (array $arr): int => count($arr);

            $result = sortBy($getCount)($data);

            expect($result)->toBe([
                'b' => [1],
                'd' => [1, 2],
                'c' => [1, 2, 3],
                'a' => [1, 2, 3, 4],
            ]);
        });
    });
});
