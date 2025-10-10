<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use ArrayIterator;

use function Cline\fp\sortWith;
use function describe;
use function expect;
use function mb_strlen;
use function strcasecmp;
use function test;

describe('sortWith', function (): void {
    describe('Happy Paths', function (): void {
        test('sorts with ascending comparator', function (): void {
            $numbers = [3, 1, 4, 1, 5, 9, 2, 6];
            $ascendingComparator = fn (int $a, int $b): int => $a <=> $b;

            $result = sortWith($ascendingComparator)($numbers);

            expect($result)->toBe([1 => 1, 3 => 1, 6 => 2, 0 => 3, 2 => 4, 4 => 5, 7 => 6, 5 => 9]);
        });

        test('sorts with descending comparator', function (): void {
            $numbers = [3, 1, 4, 1, 5, 9, 2, 6];
            $descendingComparator = fn (int $a, int $b): int => $b <=> $a;

            $result = sortWith($descendingComparator)($numbers);

            expect($result)->toBe([5 => 9, 7 => 6, 4 => 5, 2 => 4, 0 => 3, 6 => 2, 1 => 1, 3 => 1]);
        });

        test('preserves keys during sort', function (): void {
            $data = ['a' => 3, 'b' => 1, 'c' => 4, 'd' => 2];
            $ascendingComparator = fn (int $a, int $b): int => $a <=> $b;

            $result = sortWith($ascendingComparator)($data);

            expect($result)->toBe(['b' => 1, 'd' => 2, 'a' => 3, 'c' => 4]);
        });

        test('sorts strings alphabetically', function (): void {
            $words = ['delta', 'alpha', 'charlie', 'bravo'];
            $stringComparator = fn (string $a, string $b): int => $a <=> $b;

            $result = sortWith($stringComparator)($words);

            expect($result)->toBe([1 => 'alpha', 3 => 'bravo', 2 => 'charlie', 0 => 'delta']);
        });

        test('sorts strings case-insensitively', function (): void {
            $words = ['Delta', 'alpha', 'CHARLIE', 'Bravo'];
            $caseInsensitiveComparator = fn (string $a, string $b): int => strcasecmp($a, $b);

            $result = sortWith($caseInsensitiveComparator)($words);

            expect($result)->toBe([1 => 'alpha', 3 => 'Bravo', 2 => 'CHARLIE', 0 => 'Delta']);
        });
    });

    describe('Sad Paths', function (): void {
        test('handles already sorted array', function (): void {
            $sorted = [1, 2, 3, 4, 5];
            $ascendingComparator = fn (int $a, int $b): int => $a <=> $b;

            $result = sortWith($ascendingComparator)($sorted);

            expect($result)->toBe([1, 2, 3, 4, 5]);
        });

        test('handles reverse sorted array', function (): void {
            $reverseSorted = [5, 4, 3, 2, 1];
            $ascendingComparator = fn (int $a, int $b): int => $a <=> $b;

            $result = sortWith($ascendingComparator)($reverseSorted);

            expect($result)->toBe([4 => 1, 3 => 2, 2 => 3, 1 => 4, 0 => 5]);
        });

        test('handles all identical values', function (): void {
            $identical = [5, 5, 5, 5, 5];
            $ascendingComparator = fn (int $a, int $b): int => $a <=> $b;

            $result = sortWith($ascendingComparator)($identical);

            expect($result)->toBe([5, 5, 5, 5, 5]);
        });
    });

    describe('Edge Cases', function (): void {
        test('handles empty array', function (): void {
            $empty = [];
            $ascendingComparator = fn (int $a, int $b): int => $a <=> $b;

            $result = sortWith($ascendingComparator)($empty);

            expect($result)->toBe([]);
        });

        test('handles single element', function (): void {
            $single = [42];
            $ascendingComparator = fn (int $a, int $b): int => $a <=> $b;

            $result = sortWith($ascendingComparator)($single);

            expect($result)->toBe([42]);
        });

        test('sorts with complex custom comparator for objects', function (): void {
            $users = [
                ['name' => 'Alice', 'age' => 30],
                ['name' => 'Bob', 'age' => 25],
                ['name' => 'Charlie', 'age' => 35],
                ['name' => 'David', 'age' => 25],
            ];
            // Sort by age ascending, then by name ascending
            $complexComparator = function (array $a, array $b): int {
                $ageComparison = $a['age'] <=> $b['age'];

                if ($ageComparison !== 0) {
                    return $ageComparison;
                }

                return $a['name'] <=> $b['name'];
            };

            $result = sortWith($complexComparator)($users);

            expect($result)->toBe([
                1 => ['name' => 'Bob', 'age' => 25],
                3 => ['name' => 'David', 'age' => 25],
                0 => ['name' => 'Alice', 'age' => 30],
                2 => ['name' => 'Charlie', 'age' => 35],
            ]);
        });

        test('sorts iterator input', function (): void {
            $generator = function () {
                yield 'c' => 3;

                yield 'a' => 1;

                yield 'd' => 4;

                yield 'b' => 2;
            };
            $ascendingComparator = fn (int $a, int $b): int => $a <=> $b;

            $result = sortWith($ascendingComparator)($generator());

            expect($result)->toBe(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]);
        });

        test('sorts ArrayIterator instance', function (): void {
            $array = [5, 2, 8, 1, 9];
            $iterator = new ArrayIterator($array);
            $ascendingComparator = fn (int $a, int $b): int => $a <=> $b;

            $result = sortWith($ascendingComparator)($iterator);

            expect($result)->toBe([3 => 1, 1 => 2, 0 => 5, 2 => 8, 4 => 9]);
        });

        test('sorts with negative numbers', function (): void {
            $numbers = [-3, 5, -1, 0, 2, -5];
            $ascendingComparator = fn (int $a, int $b): int => $a <=> $b;

            $result = sortWith($ascendingComparator)($numbers);

            expect($result)->toBe([5 => -5, 0 => -3, 2 => -1, 3 => 0, 4 => 2, 1 => 5]);
        });

        test('sorts with custom priority order', function (): void {
            $priorities = ['medium', 'high', 'low', 'critical', 'medium'];
            $priorityOrder = ['critical' => 1, 'high' => 2, 'medium' => 3, 'low' => 4];
            $priorityComparator = fn (string $a, string $b): int => $priorityOrder[$a] <=> $priorityOrder[$b];

            $result = sortWith($priorityComparator)($priorities);

            expect($result)->toBe([
                3 => 'critical',
                1 => 'high',
                0 => 'medium',
                4 => 'medium',
                2 => 'low',
            ]);
        });

        test('sorts mixed positive and negative floats', function (): void {
            $floats = [3.14, -2.71, 0.0, 1.41, -1.73];
            $floatComparator = fn (float $a, float $b): int => $a <=> $b;

            $result = sortWith($floatComparator)($floats);

            expect($result)->toBe([1 => -2.71, 4 => -1.73, 2 => 0.0, 3 => 1.41, 0 => 3.14]);
        });

        test('sorts by string length using custom comparator', function (): void {
            $words = ['a', 'abc', 'ab', 'abcd'];
            $lengthComparator = fn (string $a, string $b): int => mb_strlen($a) <=> mb_strlen($b);

            $result = sortWith($lengthComparator)($words);

            expect($result)->toBe([0 => 'a', 2 => 'ab', 1 => 'abc', 3 => 'abcd']);
        });
    });
});
