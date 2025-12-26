<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Generator;

use function Cline\fp\reduceUntil;
use function count;
use function describe;
use function expect;
use function it;
use function mb_strlen;

describe('reduceUntil', function (): void {
    describe('Happy Path', function (): void {
        it('reduces array to sum until threshold reached', function (): void {
            $sumUntil10 = reduceUntil(
                0,
                fn (int $acc, int $n): int => $acc + $n,
                fn (int $acc): bool => $acc >= 10,
            );
            $result = $sumUntil10([3, 4, 5, 6]);
            expect($result)->toBe(12);
        });

        it('concatenates strings until length exceeds limit', function (): void {
            $concatUntilLength = reduceUntil(
                '',
                fn (string $acc, string $s): string => $acc.$s,
                fn (string $acc): bool => mb_strlen($acc) > 10,
            );
            $result = $concatUntilLength(['Hello', ' ', 'World', ' ', 'foo']);
            expect($result)->toBe('Hello World');
        });

        it('stops immediately when condition met on first element', function (): void {
            $sumUntil5 = reduceUntil(
                0,
                fn (int $acc, int $n): int => $acc + $n,
                fn (int $acc): bool => $acc >= 5,
            );
            $result = $sumUntil5([10, 20, 30]);
            expect($result)->toBe(10);
        });

        it('reduces iterable with early termination', function (): void {
            $gen = function () {
                yield 1;

                yield 2;

                yield 3;

                yield 4;

                yield 5;
            };
            $sumUntil8 = reduceUntil(
                0,
                fn (int $acc, int $n): int => $acc + $n,
                fn (int $acc): bool => $acc >= 8,
            );
            $result = $sumUntil8($gen());
            expect($result)->toBe(10);
        });

        it('accumulates product until exceeds threshold', function (): void {
            $productUntil100 = reduceUntil(
                1,
                fn (int $acc, int $n): int => $acc * $n,
                fn (int $acc): bool => $acc > 100,
            );
            $result = $productUntil100([2, 3, 4, 5]);
            expect($result)->toBe(120);
        });

        it('collects elements into array until count limit', function (): void {
            $collectUntil3 = reduceUntil(
                [],
                fn (array $acc, int $n): array => [...$acc, $n],
                fn (array $acc): bool => count($acc) >= 3,
            );
            $result = $collectUntil3([1, 2, 3, 4, 5]);
            expect($result)->toBe([1, 2, 3]);
        });
    });

    describe('Edge Cases', function (): void {
        it('processes entire array when condition never met', function (): void {
            $sumUntil100 = reduceUntil(
                0,
                fn (int $acc, int $n): int => $acc + $n,
                fn (int $acc): bool => $acc >= 100,
            );
            $result = $sumUntil100([1, 2, 3, 4, 5]);
            expect($result)->toBe(15);
        });

        it('returns initial value for empty array', function (): void {
            $sumUntil10 = reduceUntil(
                42,
                fn (int $acc, int $n): int => $acc + $n,
                fn (int $acc): bool => $acc >= 10,
            );
            $result = $sumUntil10([]);
            expect($result)->toBe(42);
        });

        it('returns initial value for empty iterable', function (): void {
            $gen = function (): Generator {
                yield from [];
            };
            $sumUntil10 = reduceUntil(
                0,
                fn (int $acc, int $n): int => $acc + $n,
                fn (int $acc): bool => $acc >= 10,
            );
            $result = $sumUntil10($gen());
            expect($result)->toBe(0);
        });

        it('handles single element array that meets condition', function (): void {
            $sumUntil5 = reduceUntil(
                0,
                fn (int $acc, int $n): int => $acc + $n,
                fn (int $acc): bool => $acc >= 5,
            );
            $result = $sumUntil5([10]);
            expect($result)->toBe(10);
        });

        it('handles single element array that does not meet condition', function (): void {
            $sumUntil100 = reduceUntil(
                0,
                fn (int $acc, int $n): int => $acc + $n,
                fn (int $acc): bool => $acc >= 100,
            );
            $result = $sumUntil100([5]);
            expect($result)->toBe(5);
        });

        it('handles negative numbers in accumulation', function (): void {
            $sumUntilNegative = reduceUntil(
                0,
                fn (int $acc, int $n): int => $acc + $n,
                fn (int $acc): bool => $acc < -5,
            );
            $result = $sumUntilNegative([-2, -3, -4, -5]);
            expect($result)->toBe(-9);
        });

        it('handles complex object accumulation', function (): void {
            $aggregateUntilLength = reduceUntil(
                (object) ['items' => [], 'total' => 0],
                fn (object $acc, int $n): object => (object) [
                    'items' => [...$acc->items, $n],
                    'total' => $acc->total + $n,
                ],
                fn (object $acc): bool => $acc->total >= 10,
            );
            $result = $aggregateUntilLength([2, 3, 4, 5]);
            expect($result->items)->toBe([2, 3, 4, 5]);
            expect($result->total)->toBe(14);
        });

        it('handles boolean stop condition with type coercion', function (): void {
            $sumUntilTruthy = reduceUntil(
                0,
                fn (int $acc, int $n): int => $acc + $n,
                fn (int $acc): bool => $acc > 0,
            );
            $result = $sumUntilTruthy([1, 2, 3]);
            expect($result)->toBe(1);
        });

        it('handles zero as valid accumulator with proper stop condition', function (): void {
            $countUntil3 = reduceUntil(
                0,
                fn (int $acc, int $_): int => $acc + 1,
                fn (int $acc): bool => $acc >= 3,
            );
            $result = $countUntil3([10, 20, 30, 40, 50]);
            expect($result)->toBe(3);
        });

        it('handles associative array as iterable', function (): void {
            $sumUntil20 = reduceUntil(
                0,
                fn (int $acc, int $n): int => $acc + $n,
                fn (int $acc): bool => $acc >= 20,
            );
            $result = $sumUntil20(['a' => 5, 'b' => 10, 'c' => 15, 'd' => 20]);
            expect($result)->toBe(30);
        });
    });
});
