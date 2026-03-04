<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\dropWhile;
use function describe;
use function expect;
use function is_int;
use function str_starts_with;
use function test;

describe('dropWhile', function (): void {
    describe('Happy Paths', function (): void {
        test('drops elements while condition is met', function (): void {
            $data = [1, 2, 3, 4, 5, 6];
            $result = dropWhile(fn (int $x): bool => $x < 4)($data);
            expect($result)->toBe([3 => 4, 4 => 5, 5 => 6]);
        });

        test('drops elements while condition is true for strings', function (): void {
            $data = ['apple', 'apricot', 'banana', 'blueberry'];
            $result = dropWhile(fn (string $s): bool => str_starts_with($s, 'a'))($data);
            expect($result)->toBe([2 => 'banana', 3 => 'blueberry']);
        });

        test('preserves keys after dropping elements', function (): void {
            $data = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
            $result = dropWhile(fn (int $x): bool => $x < 3)($data);
            expect($result)->toBe(['c' => 3, 'd' => 4]);
        });

        test('preserves numeric keys after dropping elements', function (): void {
            $data = [10 => 'first', 20 => 'second', 30 => 'third', 40 => 'fourth'];
            $result = dropWhile(fn (string $x): bool => $x !== 'third')($data);
            expect($result)->toBe([30 => 'third', 40 => 'fourth']);
        });

        test('returns empty array when condition is always true', function (): void {
            $data = [2, 4, 6, 8];
            $result = dropWhile(fn (int $x): bool => $x % 2 === 0)($data);
            expect($result)->toBe([]);
        });
    });

    describe('Sad Paths', function (): void {
        test('returns all elements when predicate is never true', function (): void {
            $data = [1, 2, 3, 4, 5];
            $result = dropWhile(fn (int $x): bool => $x > 10)($data);
            expect($result)->toBe([1, 2, 3, 4, 5]);
        });

        test('returns all elements when first element fails predicate', function (): void {
            $data = [5, 1, 2, 3, 4];
            $result = dropWhile(fn (int $x): bool => $x < 3)($data);
            expect($result)->toBe([5, 1, 2, 3, 4]);
        });

        test('continues after dropping stops even if later elements match', function (): void {
            $data = [1, 2, 10, 1, 2];
            $result = dropWhile(fn (int $x): bool => $x < 5)($data);
            expect($result)->toBe([2 => 10, 3 => 1, 4 => 2]);
        });
    });

    describe('Edge Cases', function (): void {
        test('handles empty array', function (): void {
            $data = [];
            $result = dropWhile(fn (int $x): bool => $x < 5)($data);
            expect($result)->toBe([]);
        });

        test('handles single element that matches', function (): void {
            $data = [1];
            $result = dropWhile(fn (int $x): bool => $x < 5)($data);
            expect($result)->toBe([]);
        });

        test('handles single element that does not match', function (): void {
            $data = [10];
            $result = dropWhile(fn (int $x): bool => $x < 5)($data);
            expect($result)->toBe([10]);
        });

        test('works with iterator input', function (): void {
            $generator = function () {
                yield 1;

                yield 2;

                yield 3;

                yield 10;

                yield 4;
            };
            $result = dropWhile(fn (int $x): bool => $x < 5)($generator());
            expect($result)->toBe([3 => 10, 4 => 4]);
        });

        test('works with iterator that has custom keys', function (): void {
            $generator = function () {
                yield 'a' => 1;

                yield 'b' => 2;

                yield 'c' => 3;

                yield 'd' => 10;

                yield 'e' => 4;
            };
            $result = dropWhile(fn (int $x): bool => $x < 5)($generator());
            expect($result)->toBe(['d' => 10, 'e' => 4]);
        });

        test('handles array with mixed types', function (): void {
            $data = [1, 2, 'three', 4, 5];
            $result = dropWhile(fn ($x): bool => is_int($x))($data);
            expect($result)->toBe([2 => 'three', 3 => 4, 4 => 5]);
        });

        test('handles boolean values', function (): void {
            $data = [true, true, false, true];
            $result = dropWhile(fn (bool $x): bool => $x)($data);
            expect($result)->toBe([2 => false, 3 => true]);
        });

        test('handles null values in array', function (): void {
            $data = [1, 2, null, 3, 4];
            $result = dropWhile(fn ($x): bool => $x !== null)($data);
            expect($result)->toBe([2 => null, 3 => 3, 4 => 4]);
        });
    });
});
