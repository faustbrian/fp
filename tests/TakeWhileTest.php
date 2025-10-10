<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\takeWhile;
use function describe;
use function expect;
use function is_int;
use function str_starts_with;
use function test;

describe('takeWhile', function (): void {
    describe('Happy Paths', function (): void {
        test('takes elements while condition is met', function (): void {
            $data = [1, 2, 3, 4, 5, 6];
            $result = takeWhile(fn (int $x): bool => $x < 4)($data);
            expect($result)->toBe([1, 2, 3]);
        });

        test('takes elements while condition is true for strings', function (): void {
            $data = ['apple', 'apricot', 'banana', 'blueberry'];
            $result = takeWhile(fn (string $s): bool => str_starts_with($s, 'a'))($data);
            expect($result)->toBe(['apple', 'apricot']);
        });

        test('preserves original keys when taking elements', function (): void {
            $data = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
            $result = takeWhile(fn (int $x): bool => $x < 3)($data);
            expect($result)->toBe(['a' => 1, 'b' => 2]);
        });

        test('preserves numeric keys when taking elements', function (): void {
            $data = [10 => 'first', 20 => 'second', 30 => 'third', 40 => 'fourth'];
            $result = takeWhile(fn (string $x): bool => $x !== 'third')($data);
            expect($result)->toBe([10 => 'first', 20 => 'second']);
        });

        test('takes all elements when condition is always true', function (): void {
            $data = [2, 4, 6, 8];
            $result = takeWhile(fn (int $x): bool => $x % 2 === 0)($data);
            expect($result)->toBe([2, 4, 6, 8]);
        });
    });

    describe('Sad Paths', function (): void {
        test('returns empty array when predicate is never true', function (): void {
            $data = [1, 2, 3, 4, 5];
            $result = takeWhile(fn (int $x): bool => $x > 10)($data);
            expect($result)->toBe([]);
        });

        test('returns empty array when first element fails predicate', function (): void {
            $data = [5, 1, 2, 3, 4];
            $result = takeWhile(fn (int $x): bool => $x < 3)($data);
            expect($result)->toBe([]);
        });

        test('stops immediately on first false predicate', function (): void {
            $data = [1, 2, 10, 3, 4];
            $result = takeWhile(fn (int $x): bool => $x < 5)($data);
            expect($result)->toBe([1, 2]);
        });
    });

    describe('Edge Cases', function (): void {
        test('handles empty array', function (): void {
            $data = [];
            $result = takeWhile(fn (int $x): bool => $x < 5)($data);
            expect($result)->toBe([]);
        });

        test('handles single element that matches', function (): void {
            $data = [1];
            $result = takeWhile(fn (int $x): bool => $x < 5)($data);
            expect($result)->toBe([1]);
        });

        test('handles single element that does not match', function (): void {
            $data = [10];
            $result = takeWhile(fn (int $x): bool => $x < 5)($data);
            expect($result)->toBe([]);
        });

        test('works with iterator input', function (): void {
            $generator = function () {
                yield 1;

                yield 2;

                yield 3;

                yield 10;

                yield 4;
            };
            $result = takeWhile(fn (int $x): bool => $x < 5)($generator());
            expect($result)->toBe([1, 2, 3]);
        });

        test('works with iterator that has custom keys', function (): void {
            $generator = function () {
                yield 'a' => 1;

                yield 'b' => 2;

                yield 'c' => 3;

                yield 'd' => 10;

                yield 'e' => 4;
            };
            $result = takeWhile(fn (int $x): bool => $x < 5)($generator());
            expect($result)->toBe(['a' => 1, 'b' => 2, 'c' => 3]);
        });

        test('handles array with mixed types', function (): void {
            $data = [1, 2, 'three', 4, 5];
            $result = takeWhile(fn ($x): bool => is_int($x))($data);
            expect($result)->toBe([1, 2]);
        });

        test('handles boolean values', function (): void {
            $data = [true, true, false, true];
            $result = takeWhile(fn (bool $x): bool => $x)($data);
            expect($result)->toBe([true, true]);
        });

        test('handles null values in array', function (): void {
            $data = [1, 2, null, 3, 4];
            $result = takeWhile(fn ($x): bool => $x !== null)($data);
            expect($result)->toBe([1, 2]);
        });
    });
});
