<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\amap;
use function Cline\fp\amapWithKeys;
use function Cline\fp\itmap;
use function Cline\fp\itmapWithKeys;
use function describe;
use function expect;
use function it;
use function iterator_to_array;
use function sprintf;

describe('Array Map Functions', function (): void {
    describe('Happy Path', function (): void {
        it('maps array with itmap using custom callback', function (): void {
            $result = itmap(fn (int $x): int => $x * 2)([5, 6]);
            expect(iterator_to_array($result))->toBe([10, 12]);
        });

        it('maps array with itmapWithKeys including key parameter', function (): void {
            $result = itmapWithKeys(fn (int $x, int $k): int => $x * 2 + $k)([5, 6]);
            expect(iterator_to_array($result))->toBe([10, 13]);
        });

        it('maps iterator with itmap', function (): void {
            $gen = function () {
                yield 5;

                yield 6;
            };
            $result = itmap(fn (int $x): int => $x * 2)($gen());
            expect(iterator_to_array($result))->toBe([10, 12]);
        });

        it('maps iterator with itmapWithKeys including key parameter', function (): void {
            $gen = function () {
                yield 5;

                yield 6;
            };
            $result = itmapWithKeys(fn (int $x, int $k): int => $x * 2 + $k)($gen());
            expect(iterator_to_array($result))->toBe([10, 13]);
        });

        it('maps array with amap using custom callback', function (): void {
            $result = amap(fn (int $x): int => $x * 2)([5, 6]);
            expect($result)->toBe([10, 12]);
        });

        it('maps array with amapWithKeys including key parameter', function (): void {
            $result = amapWithKeys(fn (int $x, int $k): int => $x * 2 + $k)([5, 6]);
            expect($result)->toBe([10, 13]);
        });

        it('maps iterator to array with amap', function (): void {
            $gen = function () {
                yield 5;

                yield 6;
            };
            $result = amap(fn (int $x): int => $x * 2)($gen());
            expect($result)->toBe([10, 12]);
        });

        it('maps iterator to array with amapWithKeys', function (): void {
            $gen = function () {
                yield 5;

                yield 6;
            };
            $result = amapWithKeys(fn (int $x, int $k): int => $x * 2 + $k)($gen());
            expect($result)->toBe([10, 13]);
        });

        it('preserves keys when mapping associative array with amap', function (): void {
            $a = ['a' => 'A', 'b' => 'B', 'c' => 'C'];
            $result = amap(fn (string $x): string => $x.'hi')($a);
            expect($result)->toBe(['a' => 'Ahi', 'b' => 'Bhi', 'c' => 'Chi']);
        });

        it('preserves keys when mapping associative array with amapWithKeys', function (): void {
            $a = ['a' => 'A', 'b' => 'B', 'c' => 'C'];
            $result = amapWithKeys(fn (string $v, string $k): string => $v.$k)($a);
            expect($result)->toBe(['a' => 'Aa', 'b' => 'Bb', 'c' => 'Cc']);
        });
    });

    describe('Edge Cases', function (): void {
        it('maps empty array with amap', function (): void {
            $result = amap(fn (int $x): int => $x * 2)([]);
            expect($result)->toBe([]);
        });

        it('maps single element array', function (): void {
            $result = amap(fn (int $x): int => $x * 2)([5]);
            expect($result)->toBe([10]);
        });

        it('preserves numeric keys during mapping', function (): void {
            $result = amap(fn (int $x): int => $x * 2)([10 => 5, 20 => 6]);
            expect($result)->toBe([10 => 10, 20 => 12]);
        });

        it('handles complex transformations with amapWithKeys', function (): void {
            $a = ['first' => 1, 'second' => 2, 'third' => 3];
            $result = amapWithKeys(fn (int $v, string $k): string => sprintf('%s=%d', $k, $v))($a);
            expect($result)->toBe(['first' => 'first=1', 'second' => 'second=2', 'third' => 'third=3']);
        });
    });
});
