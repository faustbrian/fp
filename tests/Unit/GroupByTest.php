<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use ArrayIterator;

use function Cline\fp\groupBy;
use function count;
use function describe;
use function expect;
use function floor;
use function get_debug_type;
use function in_array;
use function mb_strlen;
use function ord;
use function sprintf;
use function test;

describe('groupBy', function (): void {
    describe('Happy Paths', function (): void {
        test('groups numbers by even/odd', function (): void {
            $numbers = [1, 2, 3, 4, 5, 6];
            $getEvenOdd = fn (int $x): string => $x % 2 === 0 ? 'even' : 'odd';

            $result = groupBy($getEvenOdd)($numbers);

            expect($result)->toBe([
                'odd' => [0 => 1, 2 => 3, 4 => 5],
                'even' => [1 => 2, 3 => 4, 5 => 6],
            ]);
        });

        test('groups strings by first letter', function (): void {
            $words = ['apple', 'apricot', 'banana', 'blueberry', 'cherry'];
            $getFirstLetter = fn (string $word): string => $word[0];

            $result = groupBy($getFirstLetter)($words);

            expect($result)->toBe([
                'a' => [0 => 'apple', 1 => 'apricot'],
                'b' => [2 => 'banana', 3 => 'blueberry'],
                'c' => [4 => 'cherry'],
            ]);
        });

        test('groups empty array returns empty array', function (): void {
            $empty = [];
            $keyMaker = fn ($x): string => 'key';

            $result = groupBy($keyMaker)($empty);

            expect($result)->toBe([]);
        });

        test('preserves original keys within groups', function (): void {
            $data = ['a' => 10, 'b' => 20, 'c' => 15, 'd' => 25, 'e' => 12];
            $groupByTens = fn (int $x): int => (int) floor($x / 10);

            $result = groupBy($groupByTens)($data);

            expect($result)->toBe([
                1 => ['a' => 10, 'c' => 15, 'e' => 12],
                2 => ['b' => 20, 'd' => 25],
            ]);
        });

        test('groups by string property length', function (): void {
            $words = ['hi', 'hello', 'hey', 'greetings', 'hola'];
            $getLength = fn (string $word): int => mb_strlen($word);

            $result = groupBy($getLength)($words);

            expect($result)->toBe([
                2 => [0 => 'hi'],
                5 => [1 => 'hello'],
                3 => [2 => 'hey'],
                9 => [3 => 'greetings'],
                4 => [4 => 'hola'],
            ]);
        });

        test('groups user objects by role', function (): void {
            $users = [
                ['name' => 'Alice', 'role' => 'admin'],
                ['name' => 'Bob', 'role' => 'user'],
                ['name' => 'Charlie', 'role' => 'admin'],
                ['name' => 'David', 'role' => 'moderator'],
            ];
            $getRole = fn (array $user): string => $user['role'];

            $result = groupBy($getRole)($users);

            expect($result)->toBe([
                'admin' => [
                    0 => ['name' => 'Alice', 'role' => 'admin'],
                    2 => ['name' => 'Charlie', 'role' => 'admin'],
                ],
                'user' => [
                    1 => ['name' => 'Bob', 'role' => 'user'],
                ],
                'moderator' => [
                    3 => ['name' => 'David', 'role' => 'moderator'],
                ],
            ]);
        });
    });

    describe('Sad Paths', function (): void {
        test('all items group to same key', function (): void {
            $numbers = [1, 2, 3, 4, 5];
            $sameKey = fn (int $x): string => 'all';

            $result = groupBy($sameKey)($numbers);

            expect($result)->toBe([
                'all' => [1, 2, 3, 4, 5],
            ]);
        });

        test('each item groups to unique key', function (): void {
            $numbers = [10, 20, 30, 40];
            $uniqueKey = fn (int $x): int => $x;

            $result = groupBy($uniqueKey)($numbers);

            expect($result)->toBe([
                10 => [0 => 10],
                20 => [1 => 20],
                30 => [2 => 30],
                40 => [3 => 40],
            ]);
        });

        test('grouping with duplicate string keys maintains all values', function (): void {
            $data = ['x' => 1, 'y' => 2, 'z' => 3];
            $sameKey = fn (int $x): string => 'group';

            $result = groupBy($sameKey)($data);

            expect($result)->toBe([
                'group' => ['x' => 1, 'y' => 2, 'z' => 3],
            ]);
        });

        test('single element creates single group', function (): void {
            $single = [42];
            $keyMaker = fn (int $x): string => 'solo';

            $result = groupBy($keyMaker)($single);

            expect($result)->toBe([
                'solo' => [42],
            ]);
        });
    });

    describe('Edge Cases', function (): void {
        test('groups by boolean result', function (): void {
            $numbers = [1, 2, 3, 4, 5, 6];
            $isEven = fn (int $x): bool => $x % 2 === 0;

            $result = groupBy($isEven)($numbers);

            expect($result)->toBe([
                false => [0 => 1, 2 => 3, 4 => 5],
                true => [1 => 2, 3 => 4, 5 => 6],
            ]);
        });

        test('groups by null key', function (): void {
            $data = [1, 2, 3];
            $returnNull = fn (int $x): ?string => $x === 2 ? null : 'value';

            $result = groupBy($returnNull)($data);

            expect($result)->toBe([
                'value' => [0 => 1, 2 => 3],
                '' => [1 => 2], // null becomes empty string as array key
            ]);
        });

        test('groups by numeric keys', function (): void {
            $items = ['a', 'b', 'c', 'd', 'e'];
            $getNumericGroup = fn (string $x): int => ord($x) % 3;

            $result = groupBy($getNumericGroup)($items);

            expect($result)->toBe([
                1 => [0 => 'a', 3 => 'd'],
                2 => [1 => 'b', 4 => 'e'],
                0 => [2 => 'c'],
            ]);
        });

        test('groups by mixed key types', function (): void {
            $data = [1, 2, 3, 4, 5];
            $mixedKeys = fn (int $x): int|string => $x <= 2 ? $x : 'rest';

            $result = groupBy($mixedKeys)($data);

            expect($result)->toBe([
                1 => [0 => 1],
                2 => [1 => 2],
                'rest' => [2 => 3, 3 => 4, 4 => 5],
            ]);
        });

        test('groups with iterator input', function (): void {
            $generator = function () {
                yield 'first' => 1;

                yield 'second' => 2;

                yield 'third' => 3;

                yield 'fourth' => 4;
            };
            $getEvenOdd = fn (int $x): string => $x % 2 === 0 ? 'even' : 'odd';

            $result = groupBy($getEvenOdd)($generator());

            expect($result)->toBe([
                'odd' => ['first' => 1, 'third' => 3],
                'even' => ['second' => 2, 'fourth' => 4],
            ]);
        });

        test('groups ArrayIterator instance', function (): void {
            $array = [10, 20, 30, 15, 25];
            $iterator = new ArrayIterator($array);
            $groupByTens = fn (int $x): int => (int) floor($x / 10);

            $result = groupBy($groupByTens)($iterator);

            expect($result)->toBe([
                1 => [0 => 10, 3 => 15],
                2 => [1 => 20, 4 => 25],
                3 => [2 => 30],
            ]);
        });

        test('groups complex objects by property', function (): void {
            $item1 = new readonly class()
            {
                public function __construct(
                    public string $category = 'A',
                    public int $value = 10,
                ) {}
            };
            $item2 = new readonly class()
            {
                public function __construct(
                    public string $category = 'B',
                    public int $value = 20,
                ) {}
            };
            $item3 = new readonly class()
            {
                public function __construct(
                    public string $category = 'A',
                    public int $value = 30,
                ) {}
            };

            $items = [$item1, $item2, $item3];
            $getCategory = fn (object $item): string => $item->category;

            $result = groupBy($getCategory)($items);

            expect($result)->toBe([
                'A' => [0 => $item1, 2 => $item3],
                'B' => [1 => $item2],
            ]);
        });

        test('groups nested arrays by size', function (): void {
            $data = [
                'small' => [1],
                'medium' => [1, 2, 3],
                'tiny' => [],
                'large' => [1, 2, 3, 4, 5],
                'another_medium' => [4, 5, 6],
            ];
            $groupBySize = fn (array $arr): string => match (count($arr)) {
                0 => 'empty',
                1 => 'single',
                2, 3 => 'few',
                default => 'many',
            };

            $result = groupBy($groupBySize)($data);

            expect($result)->toBe([
                'single' => ['small' => [1]],
                'few' => ['medium' => [1, 2, 3], 'another_medium' => [4, 5, 6]],
                'empty' => ['tiny' => []],
                'many' => ['large' => [1, 2, 3, 4, 5]],
            ]);
        });

        test('groups with zero as both value and key', function (): void {
            $data = [0 => 0, 1 => 0, 2 => 1, 3 => 0];
            $identity = fn (int $x): int => $x;

            $result = groupBy($identity)($data);

            expect($result)->toBe([
                0 => [0 => 0, 1 => 0, 3 => 0],
                1 => [2 => 1],
            ]);
        });

        test('groups with negative numeric keys', function (): void {
            $data = [-2 => 'a', -1 => 'b', 0 => 'c', 1 => 'd'];
            $groupBySign = fn (string $x): string => in_array($x, ['a', 'b'], true) ? 'negative' : 'positive';

            $result = groupBy($groupBySign)($data);

            expect($result)->toBe([
                'negative' => [-2 => 'a', -1 => 'b'],
                'positive' => [0 => 'c', 1 => 'd'],
            ]);
        });

        test('groups with mixed value types', function (): void {
            $mixed = [1, 'string', 2.5, true, null, [], 42];
            $getType = fn ($x): string => get_debug_type($x);

            $result = groupBy($getType)($mixed);

            expect($result)->toBe([
                'int' => [0 => 1, 6 => 42],
                'string' => [1 => 'string'],
                'float' => [2 => 2.5],
                'bool' => [3 => true],
                'null' => [4 => null],
                'array' => [5 => []],
            ]);
        });

        test('groups by computed composite key', function (): void {
            $data = [
                ['type' => 'admin', 'active' => true],
                ['type' => 'user', 'active' => false],
                ['type' => 'admin', 'active' => false],
                ['type' => 'user', 'active' => true],
            ];
            $compositeKey = fn (array $item): string => sprintf('%s_%s', $item['type'], $item['active']);

            $result = groupBy($compositeKey)($data);

            expect($result)->toBe([
                'admin_1' => [0 => ['type' => 'admin', 'active' => true]],
                'user_' => [1 => ['type' => 'user', 'active' => false]],
                'admin_' => [2 => ['type' => 'admin', 'active' => false]],
                'user_1' => [3 => ['type' => 'user', 'active' => true]],
            ]);
        });
    });
});
