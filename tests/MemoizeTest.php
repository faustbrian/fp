<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_sum;
use function Cline\fp\memoize;
use function describe;
use function expect;
use function mb_strtoupper;
use function sprintf;
use function test;

describe('memoize', function (): void {
    describe('Happy Path', function (): void {
        test('caches expensive computation', function (): void {
            $callCount = 0;
            $expensiveFunction = function (int $x) use (&$callCount): int {
                ++$callCount;

                return $x * $x;
            };

            $memoized = memoize($expensiveFunction);

            // First call - executes function
            expect($memoized(5))->toBe(25);
            expect($callCount)->toBe(1);

            // Second call - uses cache
            expect($memoized(5))->toBe(25);
            expect($callCount)->toBe(1);
        });

        test('returns same result for same arguments', function (): void {
            $memoized = memoize(fn (int $x): int => $x * 2);

            $result1 = $memoized(10);
            $result2 = $memoized(10);

            expect($result1)->toBe(20);
            expect($result2)->toBe(20);
            expect($result1)->toBe($result2);
        });

        test('different arguments get different cached results', function (): void {
            $callCount = 0;
            $memoized = memoize(function (int $x) use (&$callCount): int {
                ++$callCount;

                return $x * 2;
            });

            expect($memoized(5))->toBe(10);
            expect($memoized(10))->toBe(20);
            expect($memoized(5))->toBe(10);

            // Should only be called twice (once for 5, once for 10)
            expect($callCount)->toBe(2);
        });

        test('verifies function only called once per unique args', function (): void {
            $callCount = 0;
            $memoized = memoize(function (string $str) use (&$callCount): string {
                ++$callCount;

                return mb_strtoupper($str);
            });

            $memoized('hello');
            $memoized('world');
            $memoized('hello'); // Cached
            $memoized('world'); // Cached

            expect($callCount)->toBe(2);
        });
    });

    describe('Edge Cases', function (): void {
        test('works with no arguments', function (): void {
            $callCount = 0;
            $memoized = memoize(function () use (&$callCount): int {
                ++$callCount;

                return 42;
            });

            expect($memoized())->toBe(42);
            expect($memoized())->toBe(42);
            expect($callCount)->toBe(1);
        });

        test('works with multiple arguments', function (): void {
            $callCount = 0;
            $memoized = memoize(function (int $a, int $b, int $c) use (&$callCount): int {
                ++$callCount;

                return $a + $b + $c;
            });

            expect($memoized(1, 2, 3))->toBe(6);
            expect($memoized(1, 2, 3))->toBe(6);
            expect($memoized(1, 2, 4))->toBe(7);

            expect($callCount)->toBe(2);
        });

        test('works with array arguments', function (): void {
            $callCount = 0;
            $memoized = memoize(function (array $arr) use (&$callCount): int {
                ++$callCount;

                return array_sum($arr);
            });

            expect($memoized([1, 2, 3]))->toBe(6);
            expect($memoized([1, 2, 3]))->toBe(6);
            expect($memoized([2, 3, 4]))->toBe(9);

            expect($callCount)->toBe(2);
        });

        test('works with object arguments (serialize handles it)', function (): void {
            $callCount = 0;
            $memoized = memoize(function (object $obj) use (&$callCount): string {
                ++$callCount;

                return $obj->name ?? 'unknown';
            });

            $obj1 = (object) ['name' => 'Alice'];
            $obj2 = (object) ['name' => 'Alice'];
            $obj3 = (object) ['name' => 'Bob'];

            expect($memoized($obj1))->toBe('Alice');
            expect($memoized($obj2))->toBe('Alice'); // Same serialized value
            expect($memoized($obj3))->toBe('Bob');

            expect($callCount)->toBe(2);
        });

        test('handles null arguments', function (): void {
            $callCount = 0;
            $memoized = memoize(function ($value) use (&$callCount): string {
                ++$callCount;

                return $value === null ? 'null' : 'not null';
            });

            expect($memoized(null))->toBe('null');
            expect($memoized(null))->toBe('null');
            expect($memoized('value'))->toBe('not null');

            expect($callCount)->toBe(2);
        });

        test('handles mixed argument types', function (): void {
            $callCount = 0;
            $memoized = memoize(function ($a, $b, $c) use (&$callCount): string {
                ++$callCount;

                return sprintf('%s-%s-%s', $a, $b, $c);
            });

            expect($memoized(1, 'hello', true))->toBe('1-hello-1');
            expect($memoized(1, 'hello', true))->toBe('1-hello-1');
            expect($memoized(1, 'hello', false))->toBe('1-hello-');

            expect($callCount)->toBe(2);
        });

        test('functions with side effects only execute once per unique args', function (): void {
            $sideEffects = [];
            $memoized = memoize(function (string $value) use (&$sideEffects): string {
                $sideEffects[] = $value;

                return mb_strtoupper($value);
            });

            $memoized('test');
            $memoized('test'); // Should NOT add to sideEffects
            $memoized('other');

            expect($sideEffects)->toBe(['test', 'other']);
        });

        test('multiple memoized instances are independent', function (): void {
            $fn = fn (int $x): int => $x * 2;

            $memoized1 = memoize($fn);
            $memoized2 = memoize($fn);

            $memoized1(5);

            // Each memoized instance has its own cache
            $callCount = 0;
            $memoized3 = memoize(function (int $x) use (&$callCount): int {
                ++$callCount;

                return $x * 2;
            });

            $memoized3(5);
            expect($callCount)->toBe(1);

            // Create another instance - should have separate cache
            $callCount2 = 0;
            $memoized4 = memoize(function (int $x) use (&$callCount2): int {
                ++$callCount2;

                return $x * 2;
            });

            $memoized4(5);
            expect($callCount2)->toBe(1);
        });
    });
});
