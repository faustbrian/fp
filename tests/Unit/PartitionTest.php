<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use ArrayIterator;

use function Cline\fp\partition;
use function count;
use function describe;
use function expect;
use function in_array;
use function is_numeric;
use function str_contains;
use function str_starts_with;
use function test;

describe('partition', function (): void {
    describe('Happy Paths', function (): void {
        test('partitions array with even/odd predicate', function (): void {
            $numbers = [1, 2, 3, 4, 5, 6];
            $isEven = fn (int $x): bool => $x % 2 === 0;

            [$evens, $odds] = partition($isEven)($numbers);

            expect($evens)->toBe([1 => 2, 3 => 4, 5 => 6]);
            expect($odds)->toBe([0 => 1, 2 => 3, 4 => 5]);
        });

        test('partitions empty array returns two empty arrays', function (): void {
            $empty = [];
            $predicate = fn ($x): bool => true;

            [$truthy, $falsy] = partition($predicate)($empty);

            expect($truthy)->toBe([]);
            expect($falsy)->toBe([]);
        });

        test('preserves associative array keys', function (): void {
            $data = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
            $isEven = fn (int $x): bool => $x % 2 === 0;

            [$evens, $odds] = partition($isEven)($data);

            expect($evens)->toBe(['b' => 2, 'd' => 4]);
            expect($odds)->toBe(['a' => 1, 'c' => 3]);
        });

        test('partitions with string predicate', function (): void {
            $words = ['apple', 'banana', 'apricot', 'blueberry'];
            $startsWithA = fn (string $word): bool => str_starts_with($word, 'a');

            [$startsA, $notStartsA] = partition($startsWithA)($words);

            expect($startsA)->toBe([0 => 'apple', 2 => 'apricot']);
            expect($notStartsA)->toBe([1 => 'banana', 3 => 'blueberry']);
        });

        test('partitions iterator input', function (): void {
            $generator = function () {
                yield 1;

                yield 2;

                yield 3;

                yield 4;
            };
            $isEven = fn (int $x): bool => $x % 2 === 0;

            [$evens, $odds] = partition($isEven)($generator());

            expect($evens)->toBe([1 => 2, 3 => 4]);
            expect($odds)->toBe([0 => 1, 2 => 3]);
        });

        test('partitions with custom key iterator', function (): void {
            $generator = function () {
                yield 'first' => 10;

                yield 'second' => 15;

                yield 'third' => 20;
            };
            $isGreaterThan12 = fn (int $x): bool => $x > 12;

            [$greater, $notGreater] = partition($isGreaterThan12)($generator());

            expect($greater)->toBe(['second' => 15, 'third' => 20]);
            expect($notGreater)->toBe(['first' => 10]);
        });
    });

    describe('Sad Paths', function (): void {
        test('all values match predicate returns empty falsy array', function (): void {
            $allEven = [2, 4, 6, 8];
            $isEven = fn (int $x): bool => $x % 2 === 0;

            [$truthy, $falsy] = partition($isEven)($allEven);

            expect($truthy)->toBe([2, 4, 6, 8]);
            expect($falsy)->toBe([]);
        });

        test('no values match predicate returns empty truthy array', function (): void {
            $allOdd = [1, 3, 5, 7];
            $isEven = fn (int $x): bool => $x % 2 === 0;

            [$truthy, $falsy] = partition($isEven)($allOdd);

            expect($truthy)->toBe([]);
            expect($falsy)->toBe([1, 3, 5, 7]);
        });

        test('predicate with falsy values only', function (): void {
            $values = [0, false, '', null];
            $isTruthy = fn ($x): bool => (bool) $x;

            [$truthy, $falsy] = partition($isTruthy)($values);

            expect($truthy)->toBe([]);
            expect($falsy)->toBe([0, false, '', null]);
        });

        test('predicate with truthy values only', function (): void {
            $values = [1, 'text', true, ['array']];
            $isTruthy = fn ($x): bool => (bool) $x;

            [$truthy, $falsy] = partition($isTruthy)($values);

            expect($truthy)->toBe([1, 'text', true, ['array']]);
            expect($falsy)->toBe([]);
        });
    });

    describe('Edge Cases', function (): void {
        test('partitions single element matching predicate', function (): void {
            $single = [42];
            $isEven = fn (int $x): bool => $x % 2 === 0;

            [$truthy, $falsy] = partition($isEven)($single);

            expect($truthy)->toBe([42]);
            expect($falsy)->toBe([]);
        });

        test('partitions single element not matching predicate', function (): void {
            $single = [41];
            $isEven = fn (int $x): bool => $x % 2 === 0;

            [$truthy, $falsy] = partition($isEven)($single);

            expect($truthy)->toBe([]);
            expect($falsy)->toBe([41]);
        });

        test('partitions complex objects as values', function (): void {
            $users = [
                ['name' => 'Alice', 'age' => 25, 'active' => true],
                ['name' => 'Bob', 'age' => 30, 'active' => false],
                ['name' => 'Charlie', 'age' => 35, 'active' => true],
            ];
            $isActive = fn (array $user): bool => $user['active'];

            [$active, $inactive] = partition($isActive)($users);

            expect($active)->toBe([
                0 => ['name' => 'Alice', 'age' => 25, 'active' => true],
                2 => ['name' => 'Charlie', 'age' => 35, 'active' => true],
            ]);
            expect($inactive)->toBe([
                1 => ['name' => 'Bob', 'age' => 30, 'active' => false],
            ]);
        });

        test('partitions nested arrays as values', function (): void {
            $data = [
                'first' => [1, 2, 3],
                'second' => [4, 5],
                'third' => [6],
                'fourth' => [],
            ];
            $hasMultipleElements = fn (array $arr): bool => count($arr) > 1;

            [$multiple, $notMultiple] = partition($hasMultipleElements)($data);

            expect($multiple)->toBe([
                'first' => [1, 2, 3],
                'second' => [4, 5],
            ]);
            expect($notMultiple)->toBe([
                'third' => [6],
                'fourth' => [],
            ]);
        });

        test('partitions with object instances', function (): void {
            $item1 = new readonly class()
            {
                public function __construct(
                    public int $value = 10,
                ) {}
            };
            $item2 = new readonly class()
            {
                public function __construct(
                    public int $value = 25,
                ) {}
            };
            $item3 = new readonly class()
            {
                public function __construct(
                    public int $value = 15,
                ) {}
            };

            $items = [$item1, $item2, $item3];
            $valueGreaterThan20 = fn (object $item): bool => $item->value > 20;

            [$greater, $notGreater] = partition($valueGreaterThan20)($items);

            expect($greater)->toBe([1 => $item2]);
            expect($notGreater)->toBe([0 => $item1, 2 => $item3]);
        });

        test('partitions with mixed types', function (): void {
            $mixed = [1, 'string', 2.5, true, null, [], 42];
            $isNumeric = fn ($x): bool => is_numeric($x);

            [$numeric, $notNumeric] = partition($isNumeric)($mixed);

            expect($numeric)->toBe([0 => 1, 2 => 2.5, 6 => 42]);
            expect($notNumeric)->toBe([1 => 'string', 3 => true, 4 => null, 5 => []]);
        });

        test('partitions with zero as key', function (): void {
            $data = [0 => 'zero', 1 => 'one', 2 => 'two'];
            $isEvenKey = fn (string $value): bool => in_array($value, ['zero', 'two'], true);

            [$evenKeys, $oddKeys] = partition($isEvenKey)($data);

            expect($evenKeys)->toBe([0 => 'zero', 2 => 'two']);
            expect($oddKeys)->toBe([1 => 'one']);
        });

        test('partitions with negative indices', function (): void {
            $data = [-2 => 'neg', -1 => 'also-neg', 0 => 'zero', 1 => 'pos'];
            $containsNeg = fn (string $value): bool => str_contains($value, 'neg');

            [$withNeg, $withoutNeg] = partition($containsNeg)($data);

            expect($withNeg)->toBe([-2 => 'neg', -1 => 'also-neg']);
            expect($withoutNeg)->toBe([0 => 'zero', 1 => 'pos']);
        });

        test('partitions ArrayIterator instance', function (): void {
            $array = [1, 2, 3, 4, 5];
            $iterator = new ArrayIterator($array);
            $isEven = fn (int $x): bool => $x % 2 === 0;

            [$evens, $odds] = partition($isEven)($iterator);

            expect($evens)->toBe([1 => 2, 3 => 4]);
            expect($odds)->toBe([0 => 1, 2 => 3, 4 => 5]);
        });
    });
});
