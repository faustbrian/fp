<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_combine;
use function Cline\fp\afilter;
use function Cline\fp\afilterWithKeys;
use function Cline\fp\itfilter;
use function Cline\fp\itfilterWithKeys;
use function Cline\fp\range;
use function describe;
use function expect;
use function it;
use function iterator_to_array;

describe('Array Filter Functions', function (): void {
    describe('Happy Path', function (): void {
        it('filters array with itfilter using custom callback', function (): void {
            $result = itfilter(fn (int $x): bool => $x % 2 === 0)([5, 6, 7, 8]);
            expect(iterator_to_array($result))->toBe([1 => 6, 3 => 8]);
        });

        it('filters array with itfilter using default callback', function (): void {
            $result = itfilter()([5, 0, '', 8]);
            expect(iterator_to_array($result))->toBe([0 => 5, 3 => 8]);
        });

        it('filters array with afilter using custom callback', function (): void {
            $result = afilter(fn (int $x): bool => $x % 2 === 0)([5, 6, 7, 8]);
            expect($result)->toBe([1 => 6, 3 => 8]);
        });

        it('filters iterator with afilter', function (): void {
            $gen = function () {
                yield from [5, 6, 7, 8];
            };
            $result = afilter(fn (int $x): bool => $x % 2 === 0)($gen());
            expect($result)->toBe([1 => 6, 3 => 8]);
        });

        it('filters array with afilter using default callback', function (): void {
            $result = afilter()([5, 0, '', 8]);
            expect($result)->toBe([0 => 5, 3 => 8]);
        });

        it('filters with keys using afilterWithKeys', function (): void {
            $a = array_combine(\range('a', 'd'), range(1, 4));
            $result = afilterWithKeys(static fn ($v, $k): bool => $v % 2 || $k === 'b')($a);
            expect($result)->toBe(['a' => 1, 'b' => 2, 'c' => 3]);
        });

        it('filters iterator with keys using afilterWithKeys', function (): void {
            $gen = function () {
                yield 'a' => 1;

                yield 'b' => 2;

                yield 'c' => 3;

                yield 'd' => 4;
            };
            $result = afilterWithKeys(static fn ($v, $k): bool => $v % 2 || $k === 'b')($gen());
            expect($result)->toBe(['a' => 1, 'b' => 2, 'c' => 3]);
        });

        it('filters with keys using itfilterWithKeys', function (): void {
            $a = array_combine(\range('a', 'd'), range(1, 4));
            $result = itfilterWithKeys(static fn ($v, $k): bool => $v % 2 || $k === 'b')($a);
            expect(iterator_to_array($result))->toBe(['a' => 1, 'b' => 2, 'c' => 3]);
        });
    });

    describe('Edge Cases', function (): void {
        it('filters empty array with afilter', function (): void {
            $result = afilter(fn (int $x): bool => $x % 2 === 0)([]);
            expect($result)->toBe([]);
        });

        it('filters array where no elements match predicate', function (): void {
            $result = afilter(fn (int $x): bool => $x > 100)([1, 2, 3]);
            expect($result)->toBe([]);
        });

        it('filters array where all elements match predicate', function (): void {
            $result = afilter(fn (int $x): bool => $x > 0)([1, 2, 3]);
            expect($result)->toBe([0 => 1, 1 => 2, 2 => 3]);
        });

        it('preserves original keys after filtering', function (): void {
            $result = afilter(fn (int $x): bool => $x % 2 === 0)([10 => 2, 20 => 3, 30 => 4]);
            expect($result)->toBe([10 => 2, 30 => 4]);
        });

        it('filters with default callback removing falsy values', function (): void {
            $result = afilter()([0, false, null, '', '0', [], 1, 'hello']);
            expect($result)->toBe([6 => 1, 7 => 'hello']);
        });

        it('filters single element array', function (): void {
            $result = afilter(fn (int $x): bool => $x > 0)([5]);
            expect($result)->toBe([0 => 5]);
        });
    });
});
